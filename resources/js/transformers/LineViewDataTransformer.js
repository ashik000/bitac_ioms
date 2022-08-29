export const LineViewDataTransformer = (data) => {
    const hourlyData = {
        labels: [],
        performance: [],
        availability: [],
        quality: [],
        oee: [],
        scrapped: [],
    };

    const totalSummary = {
        expected_uptime: 0,
        available_time: 0,
        produced: 0,
        expected: 0,
        scrapped: 0,
    };

    data.logs.forEach((row) => {
        const available = row.available_time / 3600;
        const performance = row.produced / row.expected;
        const quality = row.produced ? (( row.produced-row.scrapped) / row.produced) : 0;


        hourlyData.labels.unshift(`${row.hour}`.padStart(2, '0'));

        let av = (available * 100).toFixed(2);
        av = getDisplayableValueForGauge(av);
        hourlyData.availability.unshift(av);

        let pe = (performance * 100).toFixed(2);
        pe = getDisplayableValueForGauge(pe);
        hourlyData.performance.unshift(pe);

        let qu = (quality * 100).toFixed(2);
        qu = getDisplayableValueForGauge(qu);
        hourlyData.quality.unshift(qu);

        let oe = (av * pe * qu / 10000).toFixed(2);
        hourlyData.oee.unshift(oe);
        hourlyData.scrapped.unshift(row.scrapped);

        totalSummary.expected_uptime += 3600;
        totalSummary.available_time += row.available_time;
        totalSummary.produced += row.produced;
        totalSummary.expected += row.expected;
        totalSummary.scrapped += row.scrapped;
    });

    return {
        products: data.products,
        logs: data.logs,
        hourlyMetric: hourlyData,
        summaryMetric: {
            produced: totalSummary.produced,
            expected: totalSummary.expected,
            scrapped: totalSummary.scrapped,
            performance: Math.min(100, totalSummary.produced * 100 / totalSummary.expected),
            availability: totalSummary.available_time * 100 / totalSummary.expected_uptime,
            quality: totalSummary.produced ? (( totalSummary.produced-totalSummary.scrapped) / totalSummary.produced)*100 : 0,
            oee: (Math.min(1, (totalSummary.produced/totalSummary.expected)) * Math.min(1, (totalSummary.available_time / totalSummary.expected_uptime)) *
                Math.min(1, (totalSummary.produced ? (( totalSummary.produced-totalSummary.scrapped) / totalSummary.produced) : 0)))
        },
    }
};

function getDisplayableValueForGauge(value){
    value = Math.min(100,value);
    value = Math.max(value,0);
    return value;
};
