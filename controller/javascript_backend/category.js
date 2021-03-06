function saveCategorySettings(id) {
  var parallax = $('#mainparallax' + id);
  var c_id = parallax.attr("c_id");
  var c_newname = $("#categoryName" + id).val();
  var c_oldname = parallax.attr("c_name");

  var category_data = {};

  if(c_newname == ""){
    alert("Please enter a category name");
    return;
  }

  if (c_newname == c_oldname) {
    name = false;
  }

  if (background) {
    var fileSelect = document.getElementById('categoryBackground'+id);
    var file = fileSelect.files[0];
    var fileData = new FormData();
    fileData.append("bg_image", file);
    var request1 = $.ajax({
      url: 'controller/uploadFile.php',
      type: 'POST',
      contentType: false,
      processData: false,
      data: fileData
    });
    request1.done(function(response) {
      if (response == 'Your file\'s size is too large. File size must be <= 1MB') {
        alert(response);
      }
    });
  }

  var subs = {};
  $('#subs' + id).find('input').each(function () {
    if (this.type == "checkbox" && this.checked == true) {
      subs[this.name] = this.value;
    }
  });
  category_data["subs"] = subs;

  if (background) {
    var filename = document.getElementById('categoryBackground'+id).files[0]["name"];
    category_data["c_img"] = "http://" + window.location.hostname + "/bg_images/" + master_name + "/" + filename;
  }


  if (c_id == "") { //CREATE CATEGORY

    category_data["message"] = "create";
    category_data["c_newname"] = c_newname;

  } else { //UPDATE CATEGORY

    category_data["message"] = "update";
    category_data["c_id"] = c_id;
    if (name == "true") {
      category_data["c_newname"] = c_newname;
    }
  }

  var e = document.getElementById("categoryAccounts" + id);
  var social = e.options[e.selectedIndex].value;
  switch (social) {
    case 'YouTube':
      category_data["table"] = "y_subs";
      break;
    case 'Pinterest':
      category_data["table"] = "p_subs";
      break;
    case 'Reddit':
      category_data["table"] = "r_subs";
      break;
  }
  var request = $.ajax({
    url: "controller/category.php",
    type: "POST",
    data: category_data
  });

  request.done(function (response, textStatus, jqXHR) {

    var response = JSON.parse(response);

    if (response["can_update_or_create"] == "yes") {
      if (c_id == "") {
        c_id = response["c_id"];
      }
      updateSettings(id);
      parallax.attr({"c_id" : c_id});
      parallax.attr({"c_name" : c_newname});
      var c_img = response["c_img"];
      if (c_img) {
        $('#mainparallax' + id ).css('background-image', 'url("' + c_img + '")' );
      }
    }
    else {
      alert("You already have a category with named \"" + c_newname + "\". Please pick another name.");
    }
    displayUserMedia(false, social);
  });

  request.fail(function (jqXHR, textStatus, errorThrown) {
//    alert("HTTPRequest: " + textStatus + " " + errorThrown);
  });
}


function deletePanel(id) {
  var parallax = $('#mainparallax' + id);
  var c_id = parallax.attr("c_id");

  if (c_id != "") {

    var request = $.ajax({
        url: "controller/category.php",
	type: "POST",
	data: {"message" : "deleteCategory", "c_id" : c_id}
    });

  }

  deleteCategory(id);
}

function displayCheckMarks(id, c_id, table) {
  var sub_names = [];

  var sublist = $("#subs" + id);
  sublist.find('input').each(function () {
    if (this.type == "checkbox") {
      sub_names.push(this.name);
    }
  });

  var sub_name_data = {"message" : "subsInCat", "sub_names" : sub_names, "c_id" : c_id, "table" : table}
  var request = $.ajax({
    url: "controller/category.php",
    type: "POST",
    data: sub_name_data
  });

  request.done(function (response, textStatus, jqXHR) {
    var response = JSON.parse(response);
    for(var i in response) {
      var sub_name = response[i];
      var checkbox = sublist.children('[name="' + sub_name + '"]');
      checkbox.attr('checked', 'true');
    }

  });

  request.fail(function (jqXHR, textStatus, errorThrown) {
 //   alert("HTTPRequest: " + textStatus + " " + errorThrown);
  });

}
