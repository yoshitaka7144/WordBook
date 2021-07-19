$(function () {
  $("#hamburger, #close").on("click", function () {
    $("#sp-nav").toggle();
  });

  $(".data-row").on("click", function () {
    var id = $(this).children("td")[0].innerText;
    var type = $(this).children("td")[1].innerText;
    var question = $(this).children("td")[2].innerText;
    var answer = $(this).children("td")[3].innerText;

    $("#inputId").val(id);
    $("#inputType").val(type);
    $("#inputQuestion").val(question);
    $("#inputAnswer").val(answer);

    $("tr").removeClass("selected-row");
    $(this).addClass("selected-row");
  });

  $("input[name=radio]").on("change", function () {
    var type = $(this).val();

    $.ajax({
      type: "POST",
      url: "updateTable.php",
      data: {
        type: type,
        page: 1
      },
      dataType: "json"
    })
      .done(function (data) {
        console.log(data);
      })
      .fail(function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      });
  });

  // $("#btn-prev, #btn-next").on("click", function () {
  //   var type = $(this).val();

  //   $.ajax({
  //     type: "POST",
  //     url: "updateTable.php",
  //     data: {
  //       type: type,
  //       page: 1
  //     },
  //     dataType: "json"
  //   })
  //     .done(function (data) {
  //       console.log(data);
  //     })
  //     .fail(function (XMLHttpRequest, textStatus, errorThrown) {
  //       alert(errorThrown);
  //     });
  // });
});