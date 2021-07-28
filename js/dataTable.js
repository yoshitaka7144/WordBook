// データ一覧表用js
$(function () {

  // データ行クリック
  $(document).on("click", ".data-row", function () {
    // 各データ取得
    var id = $(this).children("td")[0].innerText;
    var type = $(this).children("td")[1].innerText;
    var question = $(this).children("td")[2].innerText;
    var answer = $(this).children("td")[3].innerText;

    // 編集欄に反映
    $("#inputId").val(id);
    $("#inputType").val(type);
    $("#inputQuestion").val(question);
    $("#inputAnswer").val(answer);

    // 選択した行にクラス追加
    $("tr").removeClass("selected-row");
    $(this).addClass("selected-row");
  });

  // データ表示対象が変更されたとき
  $("input[name=radio]").on("change", function () {
    // 選択された表示対象でテーブル更新
    var type = $(this).val();
    updateTable(type, 1);
  });

  // 前、次へボタンクリック
  $("#btn-prev, #btn-next").on("click", function () {
    // 選択されている表示対象
    var radioElements = $("input[name=radio]");
    radioElements = Array.from(radioElements);
    var type = radioElements.find(element => element.checked).value;

    // 表示するページ
    var currentPage = parseInt($("#current-page").text());
    var page = currentPage + ($(this).attr("id") === "btn-prev" ? -1 : 1);

    // テーブル更新
    updateTable(type, page);

  });

  /**
   * データ一覧テーブルの更新
   * @param {*} type 表示対象の種類
   * @param {*} page 表示するページ
   */
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

      // 内容クリア
      tbody.empty();

      // テーブルへ取得したデータを追加
      data["rows"].forEach(row => {
        tbody.append("<tr class='data-row'><td>" + row["id"] + "</td><td>" + row["type"] + "</td><td>" + row["question"] + "</td><td>" + row["answer"] + "</td></tr>");
      });

      // ページ数表示更新
      currentPage.text(data["currentPage"]);
      maxPage.text(data["maxPage"]);

      // 表示ページ数に対応して前、次へボタンの表示設定
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