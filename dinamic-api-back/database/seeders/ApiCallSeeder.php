<?php

namespace Database\Seeders;

use App\Models\ApiCallGroup;
use App\Models\PlatformVersion;
use Illuminate\Database\Seeder;
use App\Models\ApiCall;

class ApiCallSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener versiones
        $v202410 = PlatformVersion::where('version', '2024-10')->first();
        $v202501 = PlatformVersion::where('version', '2025-01')->first();
        $v202504 = PlatformVersion::where('version', '2025-04')->first();

        // Obtener grupos
        $collectionsGroup = ApiCallGroup::where('name', 'Collections')->first();
        $customerGroup = ApiCallGroup::where('name', 'Customers')->first();
        $ordersGroup = ApiCallGroup::where('name', 'Orders')->first();
        $productsGroup = ApiCallGroup::where('name', 'Products')->first();
        $shippingGroup = ApiCallGroup::where('name', 'Shipping')->first();
        $storePropertiesGroupGroup = ApiCallGroup::where('name', 'Store Properties')->first();

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'customerCreate',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea una nueva cuenta de cliente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'customerUpdate',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza la información de un cliente existente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'customerDelete',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina una cuenta de cliente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'customerRecover',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Envía un correo electrónico para recuperar la contraseña del cliente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'productCreate',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea un nuevo producto.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'productUpdate',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza los detalles de un producto existente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'productDelete',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina un producto.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'productVariantsBulkUpdate',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza múltiples variantes de productos.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'orderCreate',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea un nuevo pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'orderUpdate',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza los detalles de un pedido existente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'orderClose',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Cierra un pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'orderOpen',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Reabre un pedido cerrado.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'collectionCreate',
            'group_id' => $collectionsGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea una nueva colección de productos.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'collectionUpdate',
            'group_id' => $collectionsGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza los detalles de una colección existente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'collectionDelete',
            'group_id' => $collectionsGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina una colección.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'fulfillmentCreate',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea una orden de cumplimiento para un pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'fulfillmentUpdateTracking',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza la información de seguimiento.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'fulfillmentCancel',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Cancela una orden de cumplimiento.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'shopUpdate',
            'group_id' => $storePropertiesGroupGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza la configuración general de la tienda.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202410->id,
            'name' => 'shopPoliciesUpdate',
            'group_id' => $storePropertiesGroupGroup->id,
            'endpoint' => '/admin/api/2024-10/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza las políticas de la tienda.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'customerActivateByUrl',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Activa una cuenta de cliente mediante una URL de activación.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'customerResetByUrl',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Restablece la contraseña mediante una URL de restablecimiento.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'customerTagsAdd',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Agrega etiquetas a un cliente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'customerTagsRemove',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina etiquetas de un cliente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'productPublish',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Publica un producto en canales de venta específicos.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'productUnpublish',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Retira un producto de canales de venta específicos.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'productDuplicate',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea una copia de un producto existente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'orderCapturePayment',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Captura el pago de un pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'orderRefundCreate',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea un reembolso para un pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'orderRiskUpdate',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza la información de riesgo de un pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'collectionPublish',
            'group_id' => $collectionsGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Publica una colección en canales de venta.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'collectionUnpublish',
            'group_id' => $collectionsGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Retira una colección de canales de venta.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'fulfillmentServiceCreate',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea un nuevo servicio de cumplimiento.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'fulfillmentServiceUpdate',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza los detalles de un servicio de cumplimiento.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'fulfillmentServiceDelete',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina un servicio de cumplimiento.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'shopMetafieldDefinitionsCreate',
            'group_id' => $storePropertiesGroupGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea definiciones de metafields.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202501->id,
            'name' => 'shopMetafieldDefinitionsUpdate',
            'group_id' => $storePropertiesGroupGroup->id,
            'endpoint' => '/admin/api/2025-01/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza definiciones de metafields existentes.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'customerAccountCreate',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea una cuenta de cliente con autenticación mejorada.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'customerAccountUpdate',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza la información de la cuenta del cliente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'customerAccountDelete',
            'group_id' => $customerGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina una cuenta de cliente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'productMediaCreate',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Agrega medios (imágenes, videos) a un producto.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'productMediaUpdate',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza medios existentes de un producto.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'productMediaDelete',
            'group_id' => $productsGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina medios de un producto.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'orderNoteUpdate',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Agrega o actualiza una nota en un pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'orderTagsAdd',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Agrega etiquetas a un pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'orderTagsRemove',
            'group_id' => $ordersGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina etiquetas de un pedido.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'collectionImageUpdate',
            'group_id' => $collectionsGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza la imagen de una colección.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'collectionSortOrderUpdate',
            'group_id' => $collectionsGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Cambia el orden de los productos en una colección.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'shippingZoneCreate',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea una nueva zona de envío.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'shippingZoneUpdate',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Actualiza una zona de envío existente.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'shippingZoneDelete',
            'group_id' => $shippingGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina una zona de envío.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'storefrontAccessTokenCreate',
            'group_id' => $storePropertiesGroupGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Crea un token de acceso para la Storefront API.'
        ]);

        ApiCall::create([
            'platform_version_id' => $v202504->id,
            'name' => 'storefrontAccessTokenDelete',
            'group_id' => $storePropertiesGroupGroup->id,
            'endpoint' => '/admin/api/2025-04/graphql.json',
            'method' => 'MUTATION',
            'request_type' => 'graphql',
            'response_type' => 'json',
            'payload_example' => [],
            'response_example' => [],
            'description' => 'Elimina un token de acceso de la Storefront API.'
        ]);
    }
}