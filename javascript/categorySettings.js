function updateMenuName( id ){

    name = document.getElementById("categoryName" + id).value;

    document.getElementById("name" + id).innerHTML = name;

    alert( "Changing name to: " + name );

    // CHANGE NAME IN DATABASE

}

function updateBackground( id ){

    /*
    pic = document.getElementById("categoryBackground" + id).value;

    $('.parallax_main_1').css( 'background-image', 'img/snowy-mointain.jpg');
    */

    alert( "Changing background image ... " );

    // DELETE CURRENT BG_IMAGE
    // UPLOAD NEW BG_IMAGE

}

function fetchMedia(name, id) {
    var subs = $("#subs1");
    subs.empty();
    subs.append('<br>Subscriptions to Include: <br>');

    switch (name) {
        case "YouTube":
            getYouTubeSubscriptions(subs);
            break;
        case "Reddit":
            alert("Getting Reddit subreddits");
            break;
        case "Pinterest":
            alert("Getting Pinterest boards");
            break;
        default:
            break;
    }
}

function getYouTubeSubscriptions(subs) {
    var createData = {"action": "getSubs"};

    var request = $.ajax({
      url: "youtube_api/YouTube_API.php",
      type: "POST",
      data: createData
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
        var sub_data = JSON.parse(response);
       subs.append('<form class="categorySubscriptions">');
       for (var sub_name in sub_data) {
           subs.append(
               '<input type="checkbox" name="subscription" value="' + sub_name + '"> ' + sub_name + ' <br>');
       }
       subs.append('<input type="submit"></form>');
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        alert("You need to reauthorize your YouTube access. Please quit Chrome and retry again.");
    });
}
