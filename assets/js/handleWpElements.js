const wp_elements = ["#adminmenumain",".update-nag", "#wpadminbar"];
wp_elements.forEach((element) => {
    $(element).remove();
})
$("#wpcontent").css("margin-left", "0px");
$("#wpbody-content").css("padding-bottom", "0px");