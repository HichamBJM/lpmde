# Script de déploiement local sur Kubernetes (Docker Desktop)
# Usage: .\deploy-local.ps1

Write-Host "🚀 Déploiement de l'application LPMDE sur Kubernetes local..." -ForegroundColor Green

# Vérifier que kubectl est disponible
if (!(Get-Command kubectl -ErrorAction SilentlyContinue)) {
    Write-Host "❌ kubectl n'est pas installé ou n'est pas dans le PATH" -ForegroundColor Red
    exit 1
}

# Vérifier la connexion au cluster
Write-Host "📡 Vérification de la connexion au cluster..." -ForegroundColor Cyan
kubectl cluster-info | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Impossible de se connecter au cluster Kubernetes" -ForegroundColor Red
    exit 1
}

# Appliquer les manifests
Write-Host "📦 Déploiement de l'application..." -ForegroundColor Cyan
kubectl apply -f k8s/deployment.yaml
kubectl apply -f k8s/service.yaml
kubectl apply -f k8s/ingress.yaml

# Mettre à jour l'image
Write-Host "🔄 Mise à jour de l'image Docker..." -ForegroundColor Cyan
kubectl set image deployment/lpmde-app lpmde-app=ghcr.io/br-y-/lpmde_git:latest -n lpmde-production

# Redémarrer le déploiement
Write-Host "♻️  Redémarrage de l'application..." -ForegroundColor Cyan
kubectl rollout restart deployment/lpmde-app -n lpmde-production

# Attendre que le rollout soit terminé
Write-Host "⏳ Attente de la fin du déploiement..." -ForegroundColor Cyan
kubectl rollout status deployment/lpmde-app -n lpmde-production --timeout=300s

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Application déployée avec succès!" -ForegroundColor Green
    
    # Exécuter les migrations
    Write-Host "🗃️  Exécution des migrations de base de données..." -ForegroundColor Cyan
    kubectl delete job lpmde-migrations -n lpmde-production --ignore-not-found=true | Out-Null
    kubectl apply -f k8s/migration-job.yaml
    
    Write-Host "⏳ Attente de la fin des migrations..." -ForegroundColor Cyan
    kubectl wait --for=condition=complete job/lpmde-migrations -n lpmde-production --timeout=300s
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Migrations exécutées avec succès!" -ForegroundColor Green
        kubectl logs job/lpmde-migrations -n lpmde-production
    } else {
        Write-Host "⚠️  Les migrations ont échoué" -ForegroundColor Yellow
        kubectl logs job/lpmde-migrations -n lpmde-production
    }
    
    # Afficher l'état final
    Write-Host "`n📊 État du déploiement:" -ForegroundColor Cyan
    kubectl get all -n lpmde-production
    
    Write-Host "`n🌐 L'application est accessible sur: http://lpmde.local" -ForegroundColor Green
} else {
    Write-Host "❌ Le déploiement a échoué" -ForegroundColor Red
    exit 1
}
