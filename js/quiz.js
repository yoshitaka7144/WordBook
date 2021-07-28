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
        $("#answer-message").html("<i class='fas fa-check-circle'></i>正解!!!");
        $("#answer-message").addClass("color-green");
      }else{
        $("#answer-message").html("<i class='fas fa-times-circle'></i>不正解");
        $("#answer-message").addClass("color-red");
        $("input[name=incorrect-question]").val($("#text-question").text());
        $("input[name=incorrect-answer]").val($("#text-answer").text());
      }
      for(var i=1;i<=4;i++){
        if($("#choice-"+ i).val() === answer){
          $("#choice-"+ i).parent().addClass("correct");
          $("#choice-"+ i).parent().addClass("poyoyon");
        }else{
          $("#choice-"+ i).parent().addClass("incorrect");
        }
      }
      $("#btn-answer").prop('disabled', true);
      $("#btn-quiz-next").prop('disabled', false);
      $("#btn-quiz-next").addClass("poyopoyo");
    }
  });
});