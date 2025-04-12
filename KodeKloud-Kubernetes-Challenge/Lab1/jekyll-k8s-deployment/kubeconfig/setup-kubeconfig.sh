#!/bin/bash
kubectl config set-credentials martin \
  --client-key=/root/martin.key \
  --client-certificate=/root/martin.crt

kubectl config set-context developer \
  --user=martin \
  --cluster=kubernetes \
  --namespace=development

kubectl config use-context developer
