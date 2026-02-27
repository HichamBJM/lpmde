# 02 — Cycle DevSecOps

## Chaîne de valeur
Plan -> Code -> Build -> Test -> Security -> Release -> Deploy -> Observe -> Improve.

```text
[Plan] -> [Code] -> [Build] -> [Tests] -> [Security Scans]
   ^                                              |
   |                                              v
[Improve] <- [Observe/Monitor] <- [Deploy/Release]
```

## Outillage
- Plan : backlog + MoSCoW
- Code : GitHub + revue PR
- Build : Docker
- Test : PHPUnit (unit + intégration + fonctionnel)
- Security : composer audit + Trivy
- Deploy : Docker Compose (démo)
- Observe : Prometheus + Grafana
