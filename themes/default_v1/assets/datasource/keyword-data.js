var keywordData = [];
keywordData[0] = {name:"สนามบินดอนเมือง",data : []};
keywordData[1] = {name:"สนามบินสุวรรณภูมิ",data : []};
keywordData[2] = {name:"สนามบินภูเก็ต",data : []};

function keywordDataAll()
{
	var data = [];
	var startdate = getPeriodTime("3M");
	var enddate = getPeriodTime("0D");

	while(startdate<enddate) {
		var newdate = new Date(startdate);
		startdate = newdate.setDate(newdate.getDate()+1);
		startdate = newdate.getTime();
		var value = Math.floor(Math.random() * 50) + 1;
		data.push([startdate,value]);
	}

	var data2 = [];

	var starttime = startdate;
	var endtime = new Date(startdate).setHours(newdate.getHours()+23);
	while(starttime<endtime) {
		var newtime = new Date(starttime);
		var minute = Math.floor(Math.random() * 60) + 15;
		starttime = newtime.setMinutes(newdate.getMinutes()+minute);
		starttime = newtime.getTime();
		var value = Math.floor(Math.random() * 50) + 1;
		data.push([starttime,value]);
	}

	return data;
}

function keywordDataToday()
{
	var data = [];
	var starttime  = getPeriodTime("0D");
	var endtime = new Date(starttime).setHours(new Date(starttime).getHours()+23);
	while(starttime<endtime) {
		var newtime = new Date(starttime);
		var minute = Math.floor(Math.random() * 60) + 15;
		starttime = newtime.setMinutes(newtime.getMinutes()+minute);
		starttime = newtime.getTime();
		var value = Math.floor(Math.random() * 50) + 1;
		data.push([starttime,value]);
	}
	return data;
}

function searchKeywordData(keywordData,startdate,enddate)
{
	var newData = [];
	for(var i in keywordData)
	{	
		newData[i] = {};
		newData[i].data = [];
		newData[i].name = keywordData[i].name;
		var n = 0;
		for(var j in keywordData[i].data) {
			var date = keywordData[i].data[j][0];
			var value = keywordData[i].data[j][1];
			if(date >= startdate && date <= enddate) {
				newData[i].data[n] = [];
				newData[i].data[n][0] = date;
				newData[i].data[n][1] = value;
				n++;
			}
		}
	}
	return newData;
}