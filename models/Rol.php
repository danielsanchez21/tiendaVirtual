<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rol".
 *
 * @property int $id_rol
 * @property string|null $nombre
 * @property bool $estado
 *
 * @property UsuarioRol[] $usuarioRols
 */
class Rol extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rol';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'default', 'value' => null],
            [['estado'], 'default', 'value' => 1],
            [['estado'], 'boolean'],
            [['nombre'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_rol' => 'Id Rol',
            'nombre' => 'Nombre',
            'estado' => 'Estado',
        ];
    }

    /**
     * Gets query for [[UsuarioRols]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioRols()
    {
        return $this->hasMany(UsuarioRol::class, ['id_rol' => 'id_rol']);
    }

}
