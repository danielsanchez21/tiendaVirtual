<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "persona".
 *
 * @property int $id_persona
 * @property string|null $nombre
 * @property string|null $apellido
 * @property int $id_documento
 * @property string|null $telefono
 * @property string|null $direccion
 * @property string|null $genero
 *
 * @property Documento $documento
 * @property Factura[] $facturas
 */
class Persona extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'persona';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'apellido', 'telefono', 'direccion', 'genero'], 'default', 'value' => null],
            [['id_documento'], 'required'],
            [['id_documento'], 'integer'],
            [['nombre', 'apellido'], 'string', 'max' => 45],
            [['telefono'], 'string', 'max' => 15],
            [['direccion'], 'string', 'max' => 100],
            [['genero'], 'string', 'max' => 2],
            [['id_documento'], 'exist', 'skipOnError' => true, 'targetClass' => Documento::class, 'targetAttribute' => ['id_documento' => 'id_documento']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_persona' => 'Id Persona',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'id_documento' => 'Id Documento',
            'telefono' => 'Telefono',
            'direccion' => 'Direccion',
            'genero' => 'Genero',
        ];
    }

    /**
     * Gets query for [[Documento]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumento()
    {
        return $this->hasOne(Documento::class, ['id_documento' => 'id_documento']);
    }

    /**
     * Gets query for [[Facturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Factura::class, ['fk_persona' => 'id_persona']);
    }

}
