<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Functions
 *
 * @author Andres
 */
class Functions
{

    public static function getVentas($vendedor = null, $portafolio = null)
    {
        $vendedor = utf8_decode($vendedor);
        $vendedor = isset($vendedor) && !empty($vendedor) ? " AND T0.\"Vendedor_Asignado\" = '$vendedor'" : "";
        $portafolio = isset($portafolio) ? " AND T0.\"CodigoPortafolio\" = '$portafolio'" : "";
        $cadenaSQL = "SELECT \"Dia\" AS \"Dia\", SUM(\"Total_Ventas\") AS \"Total_Ventas_Diaria\", SUM(\"Total_Ventas\") - SUM(\"Costo\") AS \"Utilidad_Diaria\" FROM \"_SYS_BIC\".\"sap.casaandina.Manager/CALC_VENTASNET\" WHERE \"YEAR\" = YEAR(CURRENT_DATE) AND \"MES\" = MONTH(CURRENT_DATE) GROUP BY \"Dia\" ORDER BY \"Dia\" ASC";
        return ConectorBD::ejecutarQuery($cadenaSQL);
    }

    public static function getVentasXMes($vendedor = null, $portafolio = null)
    {
        $vendedor = utf8_decode($vendedor);
        $vendedor = isset($vendedor) && !empty($vendedor) ? " AND T0.\"Vendedor_Asignado\" = '$vendedor'" : "";
        $portafolio = isset($portafolio) ? " AND T0.\"CodigoPortafolio\" = '$portafolio'" : "";
        $cadenaSQL = "SELECT \"MES\" AS \"MES\", SUM(CASE WHEN \"YEAR\" = YEAR(CURRENT_DATE) THEN \"Total_Ventas\" ELSE 0 END) AS \"Total_Ventas_Anio_Actual\", SUM(CASE WHEN \"YEAR\" = YEAR(CURRENT_DATE) - 1 THEN \"Total_Ventas\" ELSE 0 END) AS \"Total_Ventas_Anio_Anterior\", SUM(SUM(CASE WHEN \"YEAR\" = YEAR(CURRENT_DATE) THEN \"Total_Ventas\" ELSE 0 END)) OVER (ORDER BY \"MES\") AS \"Acumulado_Anio_Actual\", SUM(SUM(CASE WHEN \"YEAR\" = YEAR(CURRENT_DATE) - 1 THEN \"Total_Ventas\" ELSE 0 END)) OVER (ORDER BY \"MES\") AS \"Acumulado_Anio_Anterior\" FROM \"_SYS_BIC\".\"sap.casaandina.Manager/CALC_VENTASNET\" WHERE \"YEAR\" >= YEAR(CURRENT_DATE) - 1 GROUP BY \"MES\" ORDER BY \"MES\" ASC";
        return ConectorBD::ejecutarQuery($cadenaSQL);
    }

}
