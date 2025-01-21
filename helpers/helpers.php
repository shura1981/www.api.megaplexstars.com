<?php

function groupArray($array, $groupkey)
{
    if (count($array) > 0) {
        $keys = array_keys($array[0]);
        $removekey = array_search($groupkey, $keys);
        if ($removekey === false)
            return array("Clave \"$groupkey\" no existe");
        else
            unset($keys[$removekey]);
        $groupcriteria = array();
        $return = array();
        foreach ($array as $value) {
            $item = null;
            foreach ($keys as $key) {
                $item[$key] = $value[$key];
            }
            $busca = array_search($value[$groupkey], $groupcriteria);
            if ($busca === false) {
                $groupcriteria[] = $value[$groupkey];
                $return[] = array($groupkey => $value[$groupkey], 'data' => array());
                $busca = count($return) - 1;
            }
            $return[$busca]['data'][] = $item;
        }
        return $return;
    } else
        return array();
}


/**
 * Valida que todos los campos requeridos estén presentes y no sean null.
 *
 * @param array $fields Datos a validar.
 * @param array $requiredFields Lista de campos obligatorios.
 * @throws InvalidArgumentException Si algún campo obligatorio falta o es null.
 */
function validateFields($fields, $requiredFields): void
{

    if (empty($fields)) {
        throw new InvalidArgumentException("El cuero de datos está vacío.");
    }
    if (empty($requiredFields)) {
        throw new InvalidArgumentException("La lista de campos obligatorios está vacía.");
    }
    foreach ($requiredFields as $field) {
        if (!isset($fields[$field]) || $fields[$field] === null) {
            throw new InvalidArgumentException("El campo obligatorio '{$field}' falta o es null.");
        }
    }
}
