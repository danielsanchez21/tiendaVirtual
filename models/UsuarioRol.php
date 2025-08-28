<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuario_rol".
 *
 * @property int $id_user_rol
 * @property int $id_usuario
 * @property int $id_rol
 *
 * @property Rol $rol
 * @property Usuario $usuario
 */
class UsuarioRol extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuario_rol';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_usuario', 'id_rol'], 'required'],
            [['id_usuario', 'id_rol'], 'integer'],
            [['id_rol'], 'exist', 'skipOnError' => true, 'targetClass' => Rol::class, 'targetAttribute' => ['id_rol' => 'id_rol']],
            [['id_usuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::class, 'targetAttribute' => ['id_usuario' => 'id_usuario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user_rol' => 'Id User Rol',
            'id_usuario' => 'Id Usuario',
            'id_rol' => 'Id Rol',
        ];
    }

    /**
     * Gets query for [[Rol]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRol()
    {
        return $this->hasOne(Rol::class, ['id_rol' => 'id_rol']);
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::class, ['id_usuario' => 'id_usuario']);
    }

}
