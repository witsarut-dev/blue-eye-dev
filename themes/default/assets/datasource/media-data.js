  var colors = Highcharts.getOptions().colors,
        categories = ['FB', 'TW', 'YB', 'WB', 'NW'],
        data = [{
            y: 25,
            url: 'realtime.html',
            color: colors[0],
            drilldown: {
                url: 'realtime.html',
                name: 'FB',
                categories: ['FB'],
                data: [30],
                color: colors[0]
            }
        }, {
            y: 25,
            url: 'realtime.html',
            color: colors[1],
            drilldown: {
                url: 'realtime.html',
                name: 'TW',
                categories: ['TW'],
                data: [20],
                color: colors[1]
            }
        }, {
            y: 20,
            url: 'realtime.html',
            color: colors[2],
            drilldown: {
                url: 'realtime.html',
                name: 'YB',
                categories: ['YB'],
                data: [25],
                color: colors[2]
            }
        }, {
            y: 10,
            url: 'realtime.html',
            color: colors[3],
            drilldown: {
                url: 'realtime.html',
                name: 'WB',
                categories: ['WB'],
                data: [25],
                color: colors[3]
            }
        }, {
            y: 20,
            url: 'realtime.html',
            color: colors[4],
            drilldown: {
                url: 'realtime.html',
                name: 'NW',
                categories: ['NW'],
                data: [15],
                color: colors[4]
            }
        }],
        periodBefore = [],
        periodCurent = [],
        i,
        j,
        dataLen = data.length,
        drillDataLen,
        brightness;


    // Build the data arrays
    for (i = 0; i < dataLen; i += 1) {

        periodCurent.push({
            name: categories[i],
            y: data[i].y,
            color: data[i].color,
            url : data[i].url
        });

        drillDataLen = data[i].drilldown.data.length;
        for (j = 0; j < drillDataLen; j += 1) {
            brightness = 0.2 - (j / drillDataLen) / 5;
            periodBefore.push({
                name: data[i].drilldown.categories[j],
                y: data[i].drilldown.data[j],
                color: Highcharts.Color(data[i].color).brighten(0.15).get(),
                url : data[i].url
            });
        }
    }
