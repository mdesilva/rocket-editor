const $ = jQuery.noConflict(); 
const supportedEditableElementTypes = "p,h1,h2,h3,h4,h5,h6,li";
const rframe = $("iframe#rocketEditorContent");
const baseUrl = document.getElementById("siteUrl").value;

new Vue({
    el: '#app',
    data: function() {
        return {
          editableElements: null,
          currentElementId: "",
          showPageLink:false,
          textToolContent: "",
          nonce: document.getElementById("nonce").value
        }
    },
    mounted: function() {
        const rframe = $("iframe#rocketEditorContent");

        $("#textTool").keyup(() => {
            //Update content on the fly
            $(`[eid=${this.currentElementId}]`, rframe.contents()).text($("#textTool").val()); //pass in the iframe document as the document we are manipulating, not the parent document which is selected by default without any arguments passed
        })

        rframe.on('load', () => {
            this.init();
        })
    },
    methods: {
        init() {
          const iFrameDoc = document.getElementById("rocketEditorContent").contentDocument; //returns null here if not same origin
          this.removeWpElementsAndWrapperElements(iFrameDoc);
          this.findEditableElements(iFrameDoc);
          this.assignIdsToEditableElements();
          this.attachListenersToEditableElements();
        },
        removeWpElementsAndWrapperElements(document){
            $("#wpadminbar", document).remove();
            $("nav", document).remove();
        },
        findEditableElements(iFrame) {
          this.editableElements = $("#rocketEditorEditableArea", iFrame).find(supportedEditableElementTypes);
        },
        assignIdsToEditableElements() {
          this.editableElements.each(function(index) {
            $(this).attr("eid", "elem" + index);
          })
        },
        removeHighlightFromSelectedElement() {
          $(`[eid=${this.currentElementId}]`, $("iframe#rocketEditorContent").contents()).css("border", "none");
          $(`[eid=${this.currentElementId}]`, $("iframe#rocketEditorContent").contents()).css("padding", "0");
        },
        attachListenersToEditableElements(){
          const $vue = this;
          this.editableElements.click(function(){ //*within es5 functions, the scope of this changes to that of the function. In this case, this refers to the element that was clicked, instead of the vue instance. 
            if ($vue.currentElementId) {
              $vue.removeHighlightFromSelectedElement();
            }
            const elemId = $(this).attr("eid"); //eid == elemId. Use a special attribute so we don't interfere with id attributes if they exist on the element
            const elemContent = $(this).text();
            $(this).css("border", "2px dotted #F44336");
            $(this).css("padding", "2px");
            $vue.textToolContent = elemContent; //pass element content to text tool prop
            $vue.currentElementId = elemId;
          })
        },
        detachIdsFromEditableElements() {
          //prepare for export
          this.editableElements.each(function() {
              $(this).removeAttr("eid");
            })
        },
        saveElement() {
          //Clear text tool content and current element, eventually save as draft
          this.removeHighlightFromSelectedElement();
          this.currentElementId = "";
          this.textToolContent = "";
        },
        exportHtml(pageId) {
          const $vue = this;
          this.saveElement();
          this.detachIdsFromEditableElements();
          const html = $("#rocketEditorEditableArea", $("iframe#rocketEditorContent").contents()).html();
          //save post to db
          $.ajax({
            method: "POST",
            url: baseUrl + "/wp-json/wp/v2/pages/"  + pageId,
            beforeSend: function(xhr) {
              xhr.setRequestHeader('X-WP-Nonce', $vue.nonce);
            },
            data: {
              'content': html
            }
          }).done(() => {
            //TODO: Handle errors
            $vue.showPageLink = true;
          })
          this.assignIdsToEditableElements(); //prepare for editing again 
        }
      }
})