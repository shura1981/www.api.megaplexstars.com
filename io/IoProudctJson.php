<?php

namespace ApiMegaplex\Io;

use ApiMegaplex\Exceptions\IoException;
use Exception;

class IoProudctJson
{
    static function saveJson($file, $data)
    {
        try {
            $idExiste = false;

            // Verificar si el fichero existe
            if (file_exists($file)) {
                // Leer el fichero existente
                $json = file_get_contents($file);
                $json_data = json_decode($json, true);
            } else {
                // Crear un array vacío si el fichero no existe
                $json_data = array();
            }

            // Buscar por ID y actualizar si existe
            foreach ($json_data as $key => $value) {
                if ($value['id'] == $data->id) {
                    $json_data[$key]['available'] = $data->available;
                    $json_data[$key]['description'] = $data->description;
                    $json_data[$key]['picture'] = $data->picture;
                    $json_data[$key]['price'] = $data->price;
                    $json_data[$key]['name'] = $data->name;
                    $idExiste = true;
                    break;
                }
            }

            // Añadir nuevo elemento si el ID no existe
            if (!$idExiste) {
                $json_data[] = array(
                    'id' => $data->id,
                    'available' => $data->available,
                    'description' => $data->description,
                    'picture' => $data->picture,
                    'price' => $data->price,
                    'name' => $data->name
                );
            }

            // Guardar el array en el fichero
            file_put_contents($file, json_encode($json_data, JSON_NUMERIC_CHECK));
            return $idExiste; // true si se actualizó, false si se añadió
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }




    }

    static function readJson($file){
        try {
            // Verificar si el fichero existe
            if (file_exists($file)) {
                // Leer el fichero existente
                $json = file_get_contents($file);
                $json_data = json_decode($json, true);
            } else {
                // Crear un array vacío si el fichero no existe
                $json_data = array();
            }
            return $json_data;
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }
    }


}