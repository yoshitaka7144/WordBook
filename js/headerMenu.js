// ヘッダーメニュー用js
$(function () {
  // スマホ用ナビメニュー表示用
  $("#hamburger, #close").on("click", function () {
    $("#sp-nav").toggle();
  });

});