<template>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-9">
          <div id="rocketEditorViewport">
            </div>
        </div>
        <div class="col-md-3" id="toolsetContainer">
            <div id="toolset">
                <h1> Editor </h1>
                <p> Click on an element to modify it </p>
                <textarea id="textTool" placeholder="Enter new content for element" v-model="textToolContent"></textarea>
            </div>
        </div>
      </div>
    </div>
</template>

<script>
// @ is an alias to /src
//import Toolset from "../components/Toolset";
import jquery from "jquery";

const $ = jquery.noConflict();
//const baseUrl = "http://localhost:8888/healthe-class/wp-json/rocket/v1/html/";
const supportedEditableElementTypes = "p,h1,h2,h3,h4,h5,h6";

export default {
  name: 'Editor',
  components: {},
      mounted: function() {
        var iframe = document.createElement("iframe");
        iframe.src = "http://localhost:8888/healthe-class";
        iframe.id = "rocketEditorContent";
    
        document.getElementById("rocketEditorViewport").appendChild(iframe);
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
          const iFrame = document.getElementById("rocketEditorContent");
          const iFrameDoc = iFrame.contentDocument; //returns null here if not same origin
          const content = iFrameDoc.querySelector(".content");
          content.id = "editableArea";
          this.removeWpElementsAndWrapperElements(iFrameDoc);
          this.findEditableElements(content);
          this.assignIdsToEditableElements();
          this.attachListenersToEditableElements();
        },
        removeWpElementsAndWrapperElements(document){
            $("#wpadminbar", document).remove();
            $("nav", document).remove();
        },
        findEditableElements(contentObj) {
          this.editableElements = $(contentObj).find(supportedEditableElementTypes);
        },
        assignIdsToEditableElements() {
          this.editableElements.each(function(index) {
            $(this).attr("eid", "elem" + index);
          })
        },
        attachListenersToEditableElements(){
          const $vue = this;
          this.editableElements.click(function(){ //*within es5 functions, the scope of this changes to that of the function. In this case, this refers to the element that was clicked, instead of the vue instance. 
            const elemId = $(this).attr("eid"); //eid == elemId. Use a special attribute so we don't interfere with id attributes if they exist on the element
            const elemContent = $(this).text();
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
          this.currentElementId = "";
          this.textToolContent = "";
        },
        exportHtml() {
          this.saveElement();
          this.detachIdsFromEditableElements();
          //const html = $("#editableArea").html();
          //save post to db
          //console.log("html save");
          this.assignIdsToEditableElements(); //prepare for editing again 
        }
      }
}

</script>
<style scoped>
#toolsetContainer {
  background-color: #518fff;
  border-top-left-radius: 10px;
  border-bottom-left-radius: 10px;
  text-align: center;
  color: white;
  
}

#rocketEditorContent {
    width: 100%;
    height: 100vh;
}
</style>