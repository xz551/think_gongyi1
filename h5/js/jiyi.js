var create_num=0;//当前第几次创建
var click_num=1;//点击方块的次数
var w_width;//窗体的宽度
var cols;//没行显示方块的数量
var select_index;//选择要变化底色的索引
var select_i=0;//出现底色的顺序
var bcolor=['bred','bgreed','byellow','bblue'];
var scolor;//方块变化底色
var gold_num;//金币数
var boxMarginTop;
var time_jiange=500;
function _init(){
    create_num=0;
    click_num=1;
    select_i=0
    select_index=[];
    gold_num=0;
    if(typeof w_width=='undefined'){
        w_width=$('body').width()-15;
        var b_height= $(document).height()  ;// document.body.clientHeight ;
        boxMarginTop=(b_height-w_width)/2;
        $('#box').css({
            'width':w_width+'px',
            'height':w_width+'px'
        });
        $('#tip').css({
            'margin-top': (boxMarginTop-30)+'px'
        });
        $('#success').css({
            'top':(boxMarginTop+boxMarginTop/2)+'px',
            'display':'none'
        });
        $('#begin').css({
            'top':(boxMarginTop+boxMarginTop/2)+'px'
        });
    }
}
function createFk(t){
    create_num++;
    $('#guanqian').text(create_num);
    var _box=$('#box');
    _box.html("");
    var _guan=create_num;
   
    cols=Math.ceil(create_num/2)+2;

    var fknum=cols*cols;
    var fkwidth=(w_width-5)/cols-5;
    for(var i=0;i<fknum;i++){
        $('<div class="fk" style="width: '+fkwidth+'px;height:'+fkwidth+'px;line-height:'+fkwidth+'px"></div>').appendTo(_box);
    }
    scolor=bcolor[ Math.floor(Math.random() *( bcolor.length))];
    if(t==1){
        createNum();
    }
}
function addEvent(){
    click_num=1;
    $('.fk').one('click',function(){
        var num=$(this).data('num');
        if(num==click_num){
            $(this).html('<img src="image/jinbi.jpg"/>'); 
            gold_num++;
            $("#suodejinbi").text(gold_num);

        }else{
            error();
        }
        if(click_num==cols){
            success();
        }else {
            click_num++;
        }
    });
}
/**
 * 挑战失败
 * */
function error(){
    $('.model').show();
    $('#success').html('挑战失败 ~~~~(>_<)~~~~ ，获得金币'+gold_num+'<br/><a href="javascript:void(0)" onclick="first_begin()">重新开始</a><br/>要按照顺序点哦，分享到朋友圈，挑战下吧').css({
        'top':'-100px',
        'fontSize':'14px'
    }).animate({
        top:(boxMarginTop+boxMarginTop/2)+'px',
        opacity: 'show'
    });
    $('.fk').each(function(){
        var num=$(this).data('num');
        if(typeof num!='undefined' || $(this).find('img').length>0){
            $(this).text(num);
        }else{
            $(this).html('<img src="image/xincui.jpg"/>');
        }
    });
    going(1);
    game_over();
}
/**
 * 挑战成功
 * */
function success(){
    $('.model').show();
    $('#success').html('恭喜您闯过第'+create_num+'关，获得金币'+gold_num+'<br/>即将挑战第'+(create_num+1)+"关").css({
        'top':'-100px',
        'fontSize':'14px'
    }).animate({
        top:(boxMarginTop+boxMarginTop/2)+'px',
        opacity: 'show'
    });
    setTimeout(function(){
        $('#success').animate({
            fontSize:'40px',
            opacity:'hide'
        });
        $('.model').hide();
        createFk(1);
    },2000);
}
/**
 * 创建要变色的方块
 * */
function createNum(){

    clearTimeout(tixingTime);

    clearTimeout(tixingTime2);

    select_index=[];
    select_i=0;
    var n=[];
    for(var i=0;i<cols*cols;i++){
        n.push(i);
    }
    var indexs=[];
    for(var j=0;j<cols;j++) {
        var index=Math.floor(Math.random() *( n.length));
        indexs.push(n[index]);
        n.splice(index,1);
    }
    select_index=indexs;
    setTimeout(function(){
        showNum();
    },time_jiange);
}

/***
 * 显示要变色的方块
 * */
function showNum(t){
    t =t|time_jiange;
    var fk=$('.fk').eq(select_index[select_i]);
    fk.addClass(scolor);
    fk.data('num',select_i+1);
    if(select_i<select_index.length-1){
        select_i++;
        setTimeout(function(){
            showNum();
        },t )
    }else{
        setTimeout(function(){
            select_i=0;
            hideNum();
        },t*2);
    }
}
function hideNum(){
    var fk=$('.fk').eq(select_index[select_i]);
    fk.removeClass(scolor);
    if(select_i<select_index.length-1){
        select_i++;
        setTimeout(function(){
            hideNum();
        },time_jiange)
    }else{
        select_i=0;
        addEvent();
        tixing();
    }
}
/**
 * 开始游戏播放ready go
 */
function ready_go(){
    document.getElementById('ready_go').play();
}
function going(t){
    if(t){
        document.getElementById('going').pause();
    }else {
        document.getElementById('going').play();
    }
}
function game_over(){
    document.getElementById('game_over').play();
}
function first_begin(){
    _init();
    $('#success').hide();
    $('.model').show();
    $('#begin').show();
    $('#begin').find('span').first().show();
    $('#begin').find('span').last().hide();
    setTimeout(function(){
        $('#begin span').toggle();
    },300);
    setTimeout(function(){
        ready_go();
    },300);
    createFk();
    setTimeout(function(){
        $('#begin').css('font-size','14px').animate({
            fontSize:'30',
            opacity: 'hide'
        });
        $('.model').hide();
        createNum();
        $('#going').get(0).play();
    },1500);
}
var tixingTime,tixingTime2;
function tixing(){
    tixingTime=setTimeout(function(){
        for(var i=0;i<select_index.length;i++){
            var fk=$('.fk').eq(select_index[i]);
            fk.addClass(scolor);
        }
    },5000);
    tixingTime2=setTimeout(function(){
        for(var i=0;i<select_index.length;i++){
            var fk=$('.fk').eq(select_index[i]);
            fk.removeClass(scolor);
        }
    },5500);

}
$(function(){
    first_begin();
});