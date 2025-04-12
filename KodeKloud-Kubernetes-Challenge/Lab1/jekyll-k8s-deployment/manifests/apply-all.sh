#!/bin/bash
kubectl apply -f manifests/01-role.yaml
kubectl apply -f manifests/02-rolebinding.yaml
kubectl apply -f manifests/03-pvc.yaml
kubectl apply -f manifests/04-pod.yaml
kubectl apply -f manifests/05-service.yaml
