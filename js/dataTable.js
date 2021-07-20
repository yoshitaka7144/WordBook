$(function () {

  $(document).on("click", ".data-row", function () {
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
    updateTable(type, 1);
  });

  $("#btn-prev, #btn-next").on("click", function () {
    var radioElements = $("input[name=radio]");
    radioElements = Array.from(radioElements);
    var type = radioElements.find(element => element.checked).value;
    var currentPage = parseInt($("#current-page").text());
    var page = currentPage + ($(this).attr("id") === "btn-prev" ? -1 : 1);
    updateTable(type, page);

  });

  function updateTable(type, page) {
    $.ajax({
      type: "POST",
      url: "updateTable.php",
      data: {
        type: type,
        page: page
      },
      dataType: "json"
    }).done(function (data) {
      var tbody = $("#data-table > tbody");
      var currentPage = $("#current-page");
      var maxPage = $("#max-page");

      tbody.empty();
      data["rows"].forEach(row => {
        tbody.append("<tr class='data-row'><td>" + row["id"] + "</td><td>" + row["type"] + "</td><td>" + row["question"] + "</td><td>" + row["answer"] + "</td></tr>");
      });
      currentPage.text(data["currentPage"]);
      maxPage.text(data["maxPage"]);

      var currentPageNum = parseInt(currentPage.text());
      var maxPageNum = parseInt(maxPage.text());
      if (currentPageNum === 1) {
        $("#btn-prev").prop('disabled', true);
        if (maxPageNum === 1) {
          $("#btn-next").prop('disabled', true);
        } else {
          $("#btn-next").prop('disabled', false);
        }
      } else {
        $("#btn-prev").prop('disabled', false);
        if (maxPageNum === currentPageNum) {
          $("#btn-next").prop('disabled', true);
        } else {
          $("#btn-next").prop('disabled', false);
        }
      }

    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    });
  }
});