import { GoogleChart } from './GoogleChart.js';

export default class EvoChart extends GoogleChart {
 #element = null;
 #component = null;
 #types = {
  area: 'AreaChart',
  table: 'Table',
  bar: 'BarChart',
  column: 'ColumnChart',
  line: 'LineChart',
  pie: 'PieChart',

 };
 constructor(id, options, dataset) {
  super();
  const elem = document.querySelector(`[data-role="chart"][data-tag="${id}"]`);
  const type = elem.getAttribute('data-type') == undefined ? null : this.#types[elem.getAttribute('data-type')];
  if (type == null) {
   console.error(`Definisikan attribute data-type di elemen ${id}`);
   return undefined;
  }
  const packages = {packages: ['corechart']};
  google.charts.load('current', packages);
  google.charts.setOnLoadCallback(() => this.setup(elem, type, options, dataset));
 }
}