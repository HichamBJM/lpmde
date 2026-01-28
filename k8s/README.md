# Déploiement Kubernetes

## 📋 Prérequis

- Un cluster Kubernetes fonctionnel
- `kubectl` installé et configuré
- Accès au cluster avec les permissions nécessaires
- Ingress Controller (NGINX) installé sur le cluster
- Cert-manager pour les certificats SSL (optionnel)

## 🔧 Configuration requise

### 1. Secrets GitHub Actions

Allez dans **Settings → Secrets and variables → Actions** et ajoutez :

- `KUBE_CONFIG` : Votre fichier kubeconfig encodé en base64
- `KUBE_NAMESPACE` : Le namespace Kubernetes (ex: `production`)

#### Générer KUBE_CONFIG :

```bash
# Encoder votre kubeconfig en base64
cat ~/.kube/config | base64 -w 0
```

### 2. Secrets Kubernetes

Créez les secrets dans votre cluster :

#### Secret pour les credentials Docker :

```bash
kubectl create secret docker-registry ghcr-secret \
  --docker-server=ghcr.io \
  --docker-username=VOTRE_USERNAME_GITHUB \
  --docker-password=VOTRE_GITHUB_TOKEN \
  -n production
```

#### Secret pour l'application :

```bash
kubectl create secret generic lpmde-secrets \
  --from-literal=database-url="mysql://user:password@mysql-service:3306/lpmde_db" \
  --from-literal=app-secret="votre-secret-symfony" \
  -n production
```

### 3. Créer le namespace :

```bash
kubectl create namespace production
```

## 🚀 Déploiement initial

### 1. Adapter les fichiers

Modifiez les fichiers dans le dossier `k8s/` :

- **deployment.yaml** : Remplacez `VOTRE-USERNAME/VOTRE-REPO` par votre repository
- **ingress.yaml** : Remplacez `production.example.com` par votre domaine

### 2. Appliquer les manifests

```bash
# Appliquer tous les manifests
kubectl apply -f k8s/ -n production

# Ou un par un
kubectl apply -f k8s/deployment.yaml -n production
kubectl apply -f k8s/service.yaml -n production
kubectl apply -f k8s/ingress.yaml -n production
```

### 3. Vérifier le déploiement

```bash
# Vérifier les pods
kubectl get pods -n production

# Vérifier les services
kubectl get services -n production

# Vérifier l'ingress
kubectl get ingress -n production

# Logs d'un pod
kubectl logs -f <pod-name> -n production
```

## 🔄 Déploiement automatique

Le déploiement automatique se fera via GitHub Actions lors d'un push sur `main` ou `master`.

Le workflow :
1. Build l'image Docker
2. Push l'image sur GitHub Container Registry
3. Met à jour le deployment Kubernetes avec la nouvelle image
4. Vérifie que le rollout s'est bien passé

## 📊 Commandes utiles

```bash
# Voir l'état du rollout
kubectl rollout status deployment/lpmde-app -n production

# Rollback vers la version précédente
kubectl rollout undo deployment/lpmde-app -n production

# Scaler l'application
kubectl scale deployment/lpmde-app --replicas=5 -n production

# Voir les événements
kubectl get events -n production --sort-by='.lastTimestamp'

# Accéder à un pod
kubectl exec -it <pod-name> -n production -- /bin/bash
```

## 🔐 Sécurité

Pour un environnement de production, considérez :

- Utiliser des secrets externes (HashiCorp Vault, AWS Secrets Manager)
- Configurer Network Policies
- Activer Pod Security Policies
- Utiliser un Ingress avec TLS/SSL
- Configurer des Resource Quotas
- Mettre en place du monitoring (Prometheus, Grafana)

## 🌐 DNS et certificats SSL

Pour configurer SSL avec Let's Encrypt :

```bash
# Installer cert-manager
kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml

# Créer un ClusterIssuer
kubectl apply -f - <<EOF
apiVersion: cert-manager.io/v1
kind: ClusterIssuer
metadata:
  name: letsencrypt-prod
spec:
  acme:
    server: https://acme-v02.api.letsencrypt.org/directory
    email: votre-email@example.com
    privateKeySecretRef:
      name: letsencrypt-prod
    solvers:
    - http01:
        ingress:
          class: nginx
EOF
```
