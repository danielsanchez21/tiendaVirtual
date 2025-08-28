<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento".
 *
 * @property int $id_documento
 * @property string|null $nombre
 * @property string|null $abreviatura
 * @property bool $estado
 *
 * @property Persona[] $personas
 */
class Documento extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'abreviatura'], 'default', 'value' => null],
            [['estado'], 'default', 'value' => 1],
            [['estado'], 'boolean'],
            [['nombre'], 'string', 'max' => 50],
            [['abreviatura'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_documento' => 'Id Documento',
            'nombre' => 'Nombre',
            'abreviatura' => 'Abreviatura',
            'estado' => 'Estado',
        ];
    }

    /**
     * Gets query for [[Personas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonas()
    {
        return $this->hasMany(Persona::class, ['id_documento' => 'id_documento']);
    }

}
