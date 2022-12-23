var ctx1 = document.getElementById('myChart').getContext('2d');
var today = new Date();
var i;
for (i = 0; i < 7; i++) {
	var dd = String(today.getDate() - i).padStart(2, '0');
	var mm = String(today.getMonth() + 1).padStart(2, '0');
	var yyyy = today.getFullYear();
	todays = dd + '/' + mm + '/' + yyyy;
	today[i]=todays;
}
var chart = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: [ today[5], today[4], today[3], today[2], today[1],today[0]],
        datasets: [{
            label: 'Last 6 Days',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data:[]
        }]
    },
	options: {}
});
function updateChart(result)
{
	for(i=0;i<result.length;i++)
	{
		chart.data.datasets[0].data[i]=result['count(id)'];
	}
	chart.update();
};






