$(function(){
  $("#hamburger, #close").on("click", function(){
    $("#sp-nav").toggle();
  });

  $(".data-row").on("click", function(){
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

  $("input[name=radio]").on("change",function(){
    var val = $(this).val();
    alert(val);
  });
});