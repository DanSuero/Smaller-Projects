$(document).ready(function(){
    function random(max){
        return Math.floor(Math.random() * Math.floor(max));
    }

    var phoneNums = [
            "7173447757",
            "7177157984",
            "7176726626"];

    var number = phoneNums.length,
        n = random(number);

    $('.contact-text').attr("href", "sms:+1"+phoneNums[n]);
    $('.contact-call').attr("href", "tel:"+phoneNums[n]);

    $('.contact-bar').on('animationend',function(){
        $('.contact-bar').css("bottom","0px");
    });

    $('.contact-close').click(function(){
        $('.contact-bar').removeAttr("style").css('transition','all ease 0.5s');

    });
});