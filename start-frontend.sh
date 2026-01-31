#!/bin/bash

# Script para iniciar el frontend Angular con la IP actual de WSL

# Obtener la IP actual de WSL
WSL_IP=$(hostname -I | awk '{print $1}')

# Actualizar el environment.ts con la IP actual
sed -i "s|apiBaseUrl: 'http://.*:8001/api'|apiBaseUrl: 'http://$WSL_IP:8001/api'|" frontend/src/environments/environment.ts

echo "IP de WSL: $WSL_IP"
echo "URL del backend: http://$WSL_IP:8001"
echo "URL del frontend: http://$WSL_IP:4201"
echo ""
echo "Frontend Angular iniciado con la IP actual de WSL..."
echo ""
echo "Desde Windows, accede a:"
echo "  Frontend: http://$WSL_IP:4201"
echo "  Backend:  http://$WSL_IP:8001"
echo ""

# Iniciar Angular
cd frontend && ng serve --host 0.0.0.0 --port 4201
