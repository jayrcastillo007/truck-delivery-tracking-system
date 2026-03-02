import './bootstrap';
import 'flowbite';

import ApexCharts from 'apexcharts'
window.ApexCharts = ApexCharts;

import { DataTable } from "simple-datatables";
window.DataTable = DataTable;

import Alpine from 'alpinejs';
window.Alpine = Alpine;

import user from './components/user';
import vehicle from './components/vehicle';
import driver from './components/driver';
import transaction from './components/transaction';

Alpine.data('user', user);
Alpine.data('vehicle', vehicle);
Alpine.data('driver', driver);
Alpine.data('transaction', transaction);

Alpine.start();
