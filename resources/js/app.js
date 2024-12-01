import DataTable from 'datatables.net-dt';

// Autres importations
import './bootstrap';
import Chart from 'chart.js/auto';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';
import 'datatables.net';
import 'datatables.net-buttons';
import 'datatables.net-responsive';
import jszip from 'jszip';
import pdfmake from 'pdfmake';

// Configuration de JSZip et toastr
window.JSZip = jszip;
window.toastr = toastr;

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
