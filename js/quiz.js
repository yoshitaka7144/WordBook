$(function () {
  $("#btn-answer").on("click", function(){
    var answer = $("#text-answer").text();
    var choice = "";
    for(var i=1;i<=4;i++){
      if($("#choice-"+ i).prop("checked")){
        choice = $("#choice-"+ i).val();
        break;
      }
    }
    $("#notification-area").css("display","block");
    if(choice === ""){
      //
      $("#answer-message").text("解答を選択して下さい。");
    }else{
      if(choice === answer){
        $("#answer-message").text("正解!!!");
        $("#answer-message").addClass("correct");
      }else{
        $("#answer-message").text("不正解");
        $("#answer-message").addClass("incorrect");
        $("input[name=incorrect-question]").val($("#text-question").text());
        $("input[name=incorrect-answer]").val($("#text-answer").text());
      }
      for(var i=1;i<=4;i++){
        if($("#choice-"+ i).val() === answer){
          $("#choice-"+ i).parent().addClass("correct");
        }else{
          $("#choice-"+ i).parent().addClass("incorrect");
        }
      }
      $("#btn-answer").prop('disabled', true);
      $("#btn-quiz-next").prop('disabled', false);
    }
  });
});