// クイズ画面用js
$(function () {
  // 解答するボタン押下時の処理
  $("#btn-answer").on("click", function () {
    // 正解
    var answer = $("#text-answer").text();

    // 選択されている選択肢の内容取得
    var choice = "";
    for (var i = 1; i <= 4; i++) {
      if ($("#choice-" + i).prop("checked")) {
        choice = $("#choice-" + i).val();
        break;
      }
    }

    // 通知欄表示
    $("#notification-area").css("display", "block");
    if (choice === "") {
      // 選択されていない場合
      $("#answer-message").text("解答を選択して下さい。");
    } else {
      // 選択されている場合
      if (choice === answer) {
        // 正解が選択されている場合
        $("#answer-message").html("<i class='fas fa-check-circle'></i>正解!!!");
        $("#answer-message").addClass("color-green");
      } else {
        // 不正解が選択されている場合
        $("#answer-message").html("<i class='fas fa-times-circle'></i>不正解");
        $("#answer-message").addClass("color-red");
        $("input[name=incorrect-question]").val($("#text-question").text());
        $("input[name=incorrect-answer]").val($("#text-answer").text());
      }

      // 選択肢に背景色設定
      for (var i = 1; i <= 4; i++) {
        if ($("#choice-" + i).val() === answer) {
          $("#choice-" + i).parent().addClass("correct");
          $("#choice-" + i).parent().addClass("poyoyon");
        } else {
          $("#choice-" + i).parent().addClass("incorrect");
        }
      }

      // ボタン表示設定
      $("#btn-answer").prop('disabled', true);
      $("#btn-quiz-next").prop('disabled', false);
      $("#btn-quiz-next").addClass("poyopoyo");
    }
  });
});