#!/bin/bash

# Script para iniciar el backend Symfony

# Obtener la IP actual de WSL
WSL_IP=$(hostname -I | awk '{print $1}')

echo "IP de WSL: $WSL_IP"
echo "Backend Symfony iniciado en el puerto 8001..."
echo ""
echo "Desde Windows, accede a:"
echo "  Backend:  http://$WSL_IP:8001"
echo ""
echo "API endpoints disponibles en:"
echo "  http://$WSL_IP:8001/api"
echo ""

# Iniciar Symfony
cd backend && symfony server:start --port=8001 --allow-all-ip
