<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Schedule Tasks
|--------------------------------------------------------------------------
|
| Aquí puedes registrar todas las tareas programadas para tu aplicación.
| Las tareas pueden ser tan simples o complejas como lo necesites.
| Esto funciona igual que la clase Schedule anterior.
|
*/

Schedule::command('membresias:verificar-vencidas')->everyMinute();