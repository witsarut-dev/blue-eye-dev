var clickedID = null;
var result_feed = true;
var result_rows = 1;

$(function() {

    //get_result_feed();
    get_keywordAndMention();
//   requestMapData();
  /*report*/
    $("#map-iframe").html('<iframe id="iframe" style="width:100%;height:500px;"></iframe>');
  setTimeout(function(){
    $("#keyword-overall li:first a").trigger("click");
  },500);

  if($("#keywordfeed .scroll-pane").size()>0) {
    $("#keywordfeed .scroll-pane").height(410).jScrollPane({autoReinitialise: true})
    .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
      if(isAtBottom) get_result_feed();
    });
  }
    if(clickedID == null){
      $('#iframe').attr('src', urlbase+'map/get_map?period='+PeriodType);
    }else{
      $('#iframe').attr('src', urlbase+'map/get_map?period='+PeriodType+'&keyword_id='+clickedID);
    }
});

function change_keywordfeed(id){
    clickedID = id;
    $('#iframe').attr('src', urlbase+'map/get_map?period='+PeriodType+'&keyword_id='+clickedID);
    result_rows = 1;
    result_feed = true;
    $("#icon-loading").show();
    get_result_feed();
    // requestMapData();
}

function get_result_feed()
{
	if(result_feed) {
		result_feed = false;
		get_feed();
	}
}

function get_feed()
{   
    var post_rows = 1;
    post_rows = result_rows;

    //requestMapData(clickedID);
    var url  = urlbase+"map/get_keywordmap_feed";
      $.ajax({
          type : 'post',
          dataType : 'html',
          data:{
              period:PeriodType,
              keyword_id:clickedID,
              post_rows:post_rows
            },
          url: url,
          cache: false,
          beforeSend: function() {
            $.fancybox.showLoading();
          },
          error: function() {
              //dialog_error("Error");
              $.fancybox.hideLoading();
              result_feed = true;
          },
          success : function(html) {
              if(result_rows==1) {
                  $("#keywordfeed .scroll-pane .jspPane").html(html);
              } else {
                  $("#keywordfeed .scroll-pane .jspPane").append(html);
              }
              if(html!="") {
                  result_feed = true; 
                  result_rows++;
                  load_fancybox();
              } else {
                  result_feed = false;
              }
              $.fancybox.hideLoading();
          }
      });
}

function get_keywordAndMention()
{
	var url  = urlbase+"map/get_keywordAndMention";
        $.ajax({
            type : 'post',
            dataType : 'html',
            data:{period:PeriodType},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
            },
            success : function(html) {
                $("#Mapkeyfil .scroll-pane").html(html);
            }
        });

}

function requestMapData()
{
    var url  = urlbase+"map/ajax_map_data";
    if (clickedID = null) {
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{period:PeriodType},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                setTimeout(requestMapData, 1000*60);
            },
            success : function(res) {
                MapData = res;
                createMarkerMap(MapData);
                setTimeout(requestMapData, 1000*60);
            }
        });
    }else {
      $.ajax({
          type : 'post',
          dataType : 'json',
          data:{period:PeriodType, keyword_id:clickedID},
          url: url,
          cache: false,
          beforeSend: function() {
          },
          error: function() {
              //dialog_error("Error");
              setTimeout(requestMapData, 1000*60);
          },
          success : function(res) {
              MapData = res;
              createMarkerMap(MapData);
              setTimeout(requestMapData, 1000*60);
          }
      });
    }

}


