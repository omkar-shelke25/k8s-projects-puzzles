#!/bin/bash
kubectl config set-credentials martin \
  --client-key=/root/martin.key \
  --client-certificate=/root/martin.crt

kubectl config set-context developer \
  --user=martin \
  --cluster=kubernetes \
  --namespace=development

#Implement After creation Of role and role binding
#kubectl config use-context developer
