$('#create-idea-tab').dblclick(function() {
    var uid = $(this).closest('#expand-tab').attr('uid');
    location.href = "Idea/Idea/ideaList/uid/" + uid + "/type/create";
});
