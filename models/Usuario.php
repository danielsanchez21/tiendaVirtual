<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuario".
 *
 * @property int $id_usuario
 * @property string|null $nombre
 * @property string|null $correo
 * @property string|null $contraseña
 *
 * @property Factura[] $facturas
 * @property UsuarioRol[] $usuarioRols
 */
class Usuario extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'correo', 'contraseña'], 'default', 'value' => null],
            [['nombre'], 'string', 'max' => 50],
            [['correo'], 'string', 'max' => 100],
            [['contraseña'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_usuario' => 'Id Usuario',
            'nombre' => 'Nombre',
            'correo' => 'Correo',
            'contraseña' => 'Contraseña',
        ];
    }

    /**
     * Gets query for [[Facturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Factura::class, ['fk_usuario' => 'id_usuario']);
    }

    /**
     * Gets query for [[UsuarioRols]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioRols()
    {
        return $this->hasMany(UsuarioRol::class, ['id_usuario' => 'id_usuario']);
    }

}
