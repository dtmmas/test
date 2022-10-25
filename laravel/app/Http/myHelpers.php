<?php

use Carbon\Carbon;
use App\Models\Setting;

function convertir_minusculas($var){
    return strtolower($var);
}

function primerdiames()
{
    return Carbon::now()->startOfMonth()->format('Y-m-d');
}

function ultimoDiaMesAnterior()
{
    return Carbon::now()->subMonth()->endOfMonth();
}

function ultimoDiaMesSumado($meses)
{
    return Carbon::now()->addMonth($meses)->endOfMonth();
}

function fecha_actual()
{
    return Carbon::now()->format('Y-m-d H:i:s');
}

function ultimoDiaMesFecha($fecha)
{
    $mañana = new Carbon($fecha);
    return $mañana->endOfMonth()->format('Y-m-d');
}

function fecha_tomorrow()
{
    $mañana = new Carbon('tomorrow');
    return $mañana->format('Y-m-d');
}

function whatsappConfig()
{ 
   // return (Object)Setting::where('type','whatsapp')->select(['name','value'])->get()->pluck('value','name')->toArray();
/* token permanente clinete 
EAAK2HlI7ZAN4BAK3SqwL79SZCQAoKyuJMPpZBihMo3YIJnFUTaX7KaBBDTICmfMOOONk6bXsm2PPYRDKAsilMtTC39TVQ4dBfz1H4BYvYpI1CF0N3OsWmUZBMZAwzYZC8PvIz15oS4me5YSabiVQOMXbaqzFwYZCrh0cMffjLmvgILqC3xY8s1b
*/
       return (Object)[
       'token'=>'EAAKp6fpdwsEBAK2gipUOT6vGALABzeCGmgVJdSlEdcSmbTcLF4ZBZBnRkOOBSbReg4RYi6YZBBsSIKiZCGayjpSLzGe0Chy2PQpQZAgs43tGOSYGN8GHUubpX0UZBaMVdZBIBzYcMJ4HlxV4EV0NITnfleI1BBuJ54fhZCFufoPd8CwOJCjmlMuJ2FYpFSKdlg3kGvKXxdgOFsmtRiIoEHwI',
      'number'=> '105095928956065',
   ];
}

function logoEmpresaConfig()
{ 
   $logo = Setting::where('type','empresa')->where('name','logo_empresa')->select('value')->first()->value;
   return ($logo!='')?Storage::url($logo):"plugins/images/logo-default.png";
}

function nombreEmpresaConfig()
{ 
   return Setting::where('type','empresa')->where('name','nombre_empresa')->select('value')->first()->value;
}

function empresaConfig()
{ 
   return (Object)Setting::where('type','empresa')->select(['name','value'])->get()->pluck('value','name')->toArray();
}
?>