<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "factura".
 *
 * @property int $id_factura
 * @property string $fecha_factura
 * @property float $valor_factura
 * @property int $fk_persona
 * @property int $fk_usuario
 * @property string $estado_factura
 *
 * @property FacturaHasProducto[] $facturaHasProductos
 * @property Persona $fkPersona
 * @property Usuario $fkUsuario
 */
class Factura extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'factura';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_factura'], 'default', 'value' => 'curdate()'],
            [['fecha_factura'], 'safe'],
            [['valor_factura', 'fk_persona', 'fk_usuario', 'estado_factura'], 'required'],
            [['valor_factura'], 'number'],
            [['fk_persona', 'fk_usuario'], 'integer'],
            [['estado_factura'], 'string', 'max' => 1],
            [['fk_persona'], 'exist', 'skipOnError' => true, 'targetClass' => Persona::class, 'targetAttribute' => ['fk_persona' => 'id_persona']],
            [['fk_usuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::class, 'targetAttribute' => ['fk_usuario' => 'id_usuario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_factura' => 'Id Factura',
            'fecha_factura' => 'Fecha Factura',
            'valor_factura' => 'Valor Factura',
            'fk_persona' => 'Fk Persona',
            'fk_usuario' => 'Fk Usuario',
            'estado_factura' => 'Estado Factura',
        ];
    }

    /**
     * Gets query for [[FacturaHasProductos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFacturaHasProductos()
    {
        return $this->hasMany(FacturaHasProducto::class, ['id_factura' => 'id_factura']);
    }

    /**
     * Gets query for [[FkPersona]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFkPersona()
    {
        return $this->hasOne(Persona::class, ['id_persona' => 'fk_persona']);
    }

    /**
     * Gets query for [[FkUsuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFkUsuario()
    {
        return $this->hasOne(Usuario::class, ['id_usuario' => 'fk_usuario']);
    }

}
