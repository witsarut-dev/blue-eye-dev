var normal_feed = true;
var priority_feed = true;
var normal_rows = 1;
var priority_rows = 1;
var is_success = 1;

$(function() {

    set_gap_time();

    const priority = document.getElementById("priority-mentions-tab");

    const element = document.getElementById("animation_main_div");

    // console.log(element.style.display);

    priority.addEventListener("click", function(e){
        e.preventDefault();
        if (element.style.display == "inline-block") {
            // console.log("hello");
            var item = document.querySelectorAll('#PriorityBox .x_content');
            var url = urlbase + "realtime/update_is_read";
            update_is_read(item,element);
        }
    });
   
    $("#filterRealtime .btn-search").click(function(){
        hide_button();
        normal_feed = true;
        priority_feed = true;
        normal_rows = 1;
        priority_rows = 1;
        is_success = 1;
        get_normal_feed();
        get_priority_feed();

        $.fancybox.close();
    });

	$("#toolMediaType button").click(function(){
        MediaType = $(this).attr("media-type");
        $("#formPeriod input[name=mediaType]").val(MediaType);
        if(MediaType=="All") {
            $("#toolMediaType button").removeClass("btn-primary").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("btn-success");
        } else {
        	$("#toolMediaType button").removeClass("btn-primary").addClass("btn-default");
            $("#toolMediaType button").removeClass("btn-success").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("btn-primary");
        }
        hide_button();
        normal_feed = true;
        priority_feed = true;
        normal_rows = 1;
        priority_rows = 1;
        is_success = 1;
        get_normal_feed();
        get_priority_feed();

    });

    $("#toolCompanyType button").click(function(){
        CompanyType = $(this).attr("company-type");
        $("#formPeriod input[name=companyType]").val(CompanyType);
        if(CompanyType=="All") {
            $("#toolCompanyType button").removeClass("btn-primary").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("btn-success");
        } else {
        	$("#toolCompanyType button").removeClass("btn-primary").addClass("btn-default");
            $("#toolCompanyType button").removeClass("btn-success").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("btn-primary");
        }
        hide_button();
        normal_feed = true;
        priority_feed = true;
        normal_rows = 1;
        priority_rows = 1;
        is_success = 1;
        get_normal_feed();
        get_priority_feed();

    });

    $(document).delegate('.btnSearchNormal,.btnSearchNegative,.btnSearchPositive', 'click', function() {
        if($(this).hasClass("search-selected")) {
            $(".panel_toolbox button").removeClass("search-selected");
            Sentiment = "";
        } else {
            $(".panel_toolbox button").removeClass("search-selected");
            $(this).addClass("search-selected");
            Sentiment = $(this).attr("sentiment");
        }
        hide_button();
        normal_feed = true;
        priority_feed = true;
        normal_rows = 1;
        priority_rows = 1;
        is_success = 1;
        get_normal_feed();
        get_priority_feed();

    });
    $("#filterOther input[name=And_filterOther]").val(OtherKeyword);
    $("#toolMediaType button[media-type='"+MediaType+"']").trigger("click");
    $(".top_search a[sentiment='"+Sentiment+"']").trigger("click");
    $("#toolCompanyType button[company-type='"+CompanyType+"']").trigger("click");
    if($("#filterOther .btn-search .bootstrap-tagsinput .tag").text() > "1"){    //other_keyword
        get_filterOther()
    }
    hide_button();

    get_normal_feed();
    get_priority_feed();

    /*realtime*/
    if($("#NormalBox .scroll-pane").size()>0) {
        $("#NormalBox .scroll-pane").height('75vh').jScrollPane({autoReinitialise: true})
        .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
            if($(".bootstrap-tagsinput .tag").text() < "1"){    //other_keyword
                if(isAtBottom && normal_feed) {
                    hide_button();
                    is_success = 3;
                    get_normal_feed();
                }
            }
        });
    }
    if($("#PriorityBox .scroll-pane").size()>0) {
        $("#PriorityBox .scroll-pane").height('75vh').jScrollPane({autoReinitialise: true})
        .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
            if($(".bootstrap-tagsinput .tag").text() < "1"){    //other_keyword
                if(isAtBottom && priority_feed) {
                    hide_button();
                    is_success = 3;
                    get_priority_feed();
                }
            }
        });
    }
    
    $("#filterOther .btn-search").click(function(){ //filterOther
        get_filterOther()
    });
  
});

function get_filterOther(){
    normal_feed = true;
    priority_feed = true;
    normal_rows = 1;
    priority_rows = 1;
    is_success = 1;
    get_normal_feed();
    get_priority_feed();
}

function get_normal_feed()
{
	if(normal_feed) {
		normal_feed = false;
		get_feed("NormalBox");
        get_is_read();
	}
}

function get_priority_feed()
{
	if(priority_feed) {
		priority_feed = false;
		get_feed("PriorityBox");
	}
}

function get_is_read() {
    var item = document.querySelectorAll('#PriorityBox .x_content');
    item.forEach(function(el) {
        el.style.backgroundColor = "#ffffff";
    });
    var url = urlbase + "realtime/get_is_read";
    // var item = document.querySelectorAll('#PriorityBox .mentions-list .item-show');
    // console.log(item);

    // get all the tab "li" elements
    const tabs = document.querySelectorAll('#mentions-tab li');

    // loop through the tabs to find the active one
    let activeTabId = null;
    tabs.forEach(tab => {
    if (tab.classList.contains('active')) {
        // found the active tab
        activeTabId = tab.firstElementChild.getAttribute('href').slice(1);
    }
    });
    $.ajax({
        type : 'post',
        dataType : 'json',
        url: url,
        error: function() {
        },
        success : function(res) {
            var count = 0;
            count = res.data.row;
            // console.log(count);
            const element = document.getElementById("animation_main_div");
            var item = document.querySelectorAll('#PriorityBox .x_content');
            // console.log(item);
            if (activeTabId == "normal-mentions" && count != 0) {
                element.style.display = "inline-block";
                if (item.length > 0) {
                    for (let index = 0; index <= (count-1); index++) {
                        item[index].style.backgroundColor = "#ffe4c475";
                    }
                }
            }else if(activeTabId == "priority-mentions" && count != 0){
                if (item.length > 0) {
			  
                    for (let index = 0; index <= (count-1); index++) {
                        item[index].style.backgroundColor = "#ffe4c475";
                    }
                    update_is_read(item,element);
                }
            }
        }
    });
}

function update_is_read(item,element) {
    var url = urlbase + "realtime/update_is_read";
    $.ajax({
        type : 'post',
        dataType : 'json',
        url: url,
        error: function() {
        },
        success : function(res) {
            // var timer;
            // clearTimeout(timer)
            element.style.display = "none";
            // timer = setTimeout(function() {
            //     item.forEach(function(el) {
            //         el.style.backgroundColor = "#ffffff";
            //     });
            // }, 1000 * 60);
        }
    });
}

function get_feed(post_type)
{
	var post_rows = 1;
	var url  = urlbase+"realtime/get_feed";
    var keyword_in = [];
    var other_keyword = [];
    $("input[name^=keyword_id]:checked").each(function(){
        keyword_in.push($(this).val());
    });
    if($(".bootstrap-tagsinput .tag").text() > "1"){    //other_keyword
        $(".bootstrap-tagsinput .tag").each(function(){
            other_keyword.push($(this).text());
            $("#formPeriod input[name=other_keyword]").val(other_keyword);
        });
    }

	if(post_type=="NormalBox") {
        post_rows = normal_rows;
        if (CompanyType == "All" && is_success == 3) post_rows -= 1;
    }
    if(post_type=="PriorityBox") {
        post_rows = priority_rows;
        if (CompanyType == "All" && is_success == 3) post_rows -= 1;
    }

    $.ajax({
        type : 'post',
        dataType : 'html',
        data : {post_type:post_type,
                post_rows:post_rows,
                media_type:MediaType,
                company_type:CompanyType,
                sentiment:Sentiment,
                keyword:GetKeyword,
                time:GetTime,
                period_type:PeriodType,
                'keyword_in[]':keyword_in,
                'other_keyword[]':other_keyword
        },
        url : url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            if(post_type == "NormalBox") normal_feed = true;
            if(post_type == "PriorityBox") priority_feed = true;
        },
        success : function(html) {
            if(post_rows == 1)  {
                $("#"+post_type+" .scroll-pane .jspPane").html(html);
            } else {
                $("#"+post_type+" .scroll-pane .jspPane").append(html);
            }
        	if(html!="") {
            	if(post_type=="NormalBox") {normal_feed = true; normal_rows++;}
        		if(post_type=="PriorityBox") {priority_feed = true; priority_rows++;}
        		load_fancybox();
            } else {
            	if(post_type=="NormalBox") normal_feed = false;
        		if(post_type=="PriorityBox") priority_feed = false;
            }
            if(is_success==3 || is_success==1) {
                $.fancybox.hideLoading();
                show_button();
            } else {
                is_success++;
            }
            get_is_read();
        }
    });
}

function add_feed(post_type)
{
    var last_time = 1;
    var url  = urlbase+"realtime/add_feed";
    var keyword_in = [];
    var other_keyword = [];
    $("input[name^=keyword_id]:checked").each(function(){
        keyword_in.push($(this).val());
    });
    if($(".bootstrap-tagsinput .tag").text() > "1"){    //other_keyword
        $(".bootstrap-tagsinput .tag").each(function(){
            other_keyword.push($(this).text());
        });
    }
    last_time = $("#"+post_type+" .item-show:first gap.post-time").attr("time");

    $.ajax({
        type : 'post',
        dataType : 'html',
        data : {post_type:post_type,
                last_time:last_time,
                media_type:MediaType,
                company_type:CompanyType,
                sentiment:Sentiment,
                period_type:PeriodType,
                'keyword_in[]':keyword_in,
                'other_keyword[]':other_keyword
        },
        url : url,
        beforeSend: function() {
        },
        error: function() {
        },
        success : function(html) {
            $("#"+post_type+" .scroll-pane .jspPane").prepend(html);
            load_fancybox();
        }
    });
}

function hide_button()
{
    $("#toolMediaType button").attr("disabled","disabled");
    $("#toolCompanyType button").attr("disabled","disabled");
    $('.btnSearchNormal,.btnSearchNegative,.btnSearchPositive').attr("disabled","disabled");
}

function show_button()
{
    $("#toolMediaType button").removeAttr("disabled");
    $("#toolCompanyType button").removeAttr("disabled");
    $('.btnSearchNormal,.btnSearchNegative,.btnSearchPositive').removeAttr("disabled");
}