<?php

    // Crear una instancia del objeto SAPbobsCOM.Company
    $company = new COM("SAPbobsCOM.Company");

    // Configurar los parámetros de conexión
    $company->Server = "190.147.111.202:30015"; // IP del servidor
    $company->DbServerType = 9; // Tipo de servidor para HANA
    $company->CompanyDB = "SBOPRUEBASCA"; // Nombre de la base de datos
    $company->UserName = "manager"; // Usuario de SAP
    $company->Password = "HYC909"; // Contraseña de SAP
    $company->DbUserName = "SYSTEM"; // Usuario de la base de datos
    $company->DbPassword = "Asdf1234$"; // Contraseña de la base de datos
    $company->UseTrusted = false; // Usar autenticación de SQL Server (false para usar autenticación SQL en lugar de Windows)

    // Establecer la conexión
    $connectionResult = $company->Connect();

    if ($connectionResult != 0) {
        // Si hay un error, obtener el código y el mensaje de error
        $errorCode = $company->GetLastErrorCode();
        $errorMessage = $company->GetLastErrorDescription();
        echo "Error de conexión: [$errorCode] $errorMessage";
    } else {
        echo "Conexión exitosa a SAP Business One usando DI API";

        // Aquí puedes proceder a realizar operaciones con la DI API
        // Por ejemplo, acceder a la tabla de artículos OITM
        $item = $company->GetBusinessObject(4); // 4 es el código para objetos de tipo artículo (OITM)

        // Consulta de un artículo específico
        $itemCode = "A00001"; // Código del artículo
        if ($item->GetByKey($itemCode)) {
            echo "Nombre del artículo: " . $item->ItemName;
        } else {
            echo "Artículo no encontrado.";
        }

        // Desconectar cuando hayas terminado
        $company->Disconnect();
    }
