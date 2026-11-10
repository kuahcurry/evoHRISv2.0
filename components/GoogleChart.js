export class GoogleChart {
 setup(elem, type, options, dataset) {
  const data = google.visualization.arrayToDataTable(dataset);
  const chart = new google.visualization[type](elem);
  chart.draw(data, options);
 }
}
