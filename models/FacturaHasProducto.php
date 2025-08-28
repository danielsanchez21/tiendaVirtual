<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "factura_has_producto".
 *
 * @property int $id_factura_has_producto
 * @property int $id_factura
 * @property int $id_producto
 * @property int $cantidad
 * @property float $valor
 *
 * @property Factura $factura
 * @property Producto $producto
 */
class FacturaHasProducto extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'factura_has_producto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_factura', 'id_producto', 'cantidad', 'valor'], 'required'],
            [['id_factura', 'id_producto', 'cantidad'], 'integer'],
            [['valor'], 'number'],
            [['id_factura'], 'exist', 'skipOnError' => true, 'targetClass' => Factura::class, 'targetAttribute' => ['id_factura' => 'id_factura']],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => Producto::class, 'targetAttribute' => ['id_producto' => 'id_Producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_factura_has_producto' => 'Id Factura Has Producto',
            'id_factura' => 'Id Factura',
            'id_producto' => 'Id Producto',
            'cantidad' => 'Cantidad',
            'valor' => 'Valor',
        ];
    }

    /**
     * Gets query for [[Factura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFactura()
    {
        return $this->hasOne(Factura::class, ['id_factura' => 'id_factura']);
    }

    /**
     * Gets query for [[Producto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Producto::class, ['id_Producto' => 'id_producto']);
    }

}
