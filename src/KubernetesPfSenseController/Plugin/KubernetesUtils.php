<?php

namespace KubernetesPfSenseController\Plugin;

/**
 * Used to interact with/parse Kubernetes resources
 *
 * Class KubernetesUtils
 * @package KubernetesPfSenseController\Plugin
 */
class KubernetesUtils
{
    /**
     * Annotation used to set hostname on services (ie: LoadBalancer services)
     */
    const SERVICE_HOSTNAME_ANNOTATION = 'dns.pfsense.org/hostname';

    public static function getResourceAnnotationValue($resource, $annotation)
    {
        if (!key_exists('annotations', $resource['metadata'])) {
            return;
        }

        foreach ($resource['metadata']['annotations'] as $key => $value) {
            if ($key == $annotation) {
                return $value;
            }
        }
    }

    /**
     * If annotation is present on the resource
     *
     * @param $resource
     * @param $annotation
     * @return bool
     */
    public static function getResourceAnnotationExists($resource, $annotation)
    {
        if (!key_exists('annotations', $resource['metadata'])) {
            return false;
        }

        foreach ($resource['metadata']['annotations'] as $key => $value) {
            if ($key == $annotation) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve specific item from API list response
     *
     * @param $resources
     * @param $namespace
     * @param $name
     * @return mixed
     */
    public static function getResourceByNamespaceName($resources, $namespace, $name)
    {
        foreach ($resources as $resource) {
            if ($resource['metadata']['namespace'] == $namespace && $resource['metadata']['name'] == $name) {
                return $resource;
            }
        }
    }

    /**
     * Get selfLink property from resource
     *
     * @param $resource
     * @return mixed
     */
    public static function getResourceSelfLink($resource)
    {
        return $resource['metadata']['selfLink'];
    }

    /**
     * Get namespace property from resource
     *
     * @param $resource
     * @return mixed
     */
    public static function getResourceNamespace($resource)
    {
        return $resource['metadata']['namespace'];
    }

    /**
     * Get name property from resource
     *
     * @param $resource
     * @return mixed
     */
    public static function getResourceName($resource)
    {
        return $resource['metadata']['name'];
    }

    public static function getResourceNamespaceHyphenName($resource)
    {
        return self::getResourceNamespace($resource).'-'.self::getResourceName($resource);
    }

    /**
     * Get IP address of a node resource
     *
     * @param $node
     * @return mixed
     */
    public static function getNodeIp($node)
    {
        foreach ($node['status']['addresses'] as $address) {
            if ($address['type'] == 'InternalIP') {
                return $address['address'];
            }
        }
    }

    /**
     * Get the key/item from a list by name
     *
     * @param $list
     * @param $name
     * @return array
     */
    public static function findListItem($list, $name, $namespace = null)
    {
        $itemKey = null;
        $item = null;
        foreach ($list as $key => $value) {
            if ($value['metadata']['name'] == $name) {
                if (!empty($namespace)) {
                    if ($value['metadata']['namespace'] == $namespace) {
                        $itemKey = $key;
                        $item = $value;
                        break;
                    }
                } else {
                    $itemKey = $key;
                    $item = $value;
                    break;
                }
            }
        }

        return [
            'key' => $itemKey,
            'item' => $item
        ];
    }

    /**
     * Get IP address of a service resource
     *
     * @param $service
     * @return mixed
     */
    public static function getServiceIp($service)
    {
        return $service['status']['loadBalancer']['ingress'][0]['ip'];
    }

    /**
     * Get IP address of an ingress resource
     *
     * @param $ingress
     * @return mixed
     */
    public static function getIngressIp($ingress)
    {
        return $ingress['status']['loadBalancer']['ingress'][0]['ip'];
    }

    /**
     * Get hostname of a service
     *
     * @param $service
     * @return mixed
     */
    public static function getServiceHostname($service)
    {
        foreach ($service['metadata']['annotations'] as $key => $value) {
            if ($key == self::SERVICE_HOSTNAME_ANNOTATION) {
                return $value;
            }
        }
    }
}
