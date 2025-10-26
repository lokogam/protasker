#!/bin/bash

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🐳 Protasker Docker Setup${NC}"
echo "================================"

# Función para mostrar ayuda
show_help() {
    echo "Uso: ./docker.sh [COMANDO]"
    echo ""
    echo "Comandos disponibles:"
    echo "  setup     - Configuración inicial del proyecto"
    echo "  start     - Iniciar todos los servicios"
    echo "  stop      - Detener todos los servicios"
    echo "  restart   - Reiniciar todos los servicios"
    echo "  logs      - Ver logs de todos los servicios"
    echo "  backend   - Entrar al contenedor del backend"
    echo "  frontend  - Entrar al contenedor del frontend"
    echo "  mysql     - Entrar al contenedor de MySQL"
    echo "  fresh     - Resetear base de datos con seeders"
    echo "  build     - Reconstruir imágenes Docker"
    echo "  clean     - Limpiar contenedores e imágenes"
    echo "  status    - Ver estado de los servicios"
    echo "  help      - Mostrar esta ayuda"
}

# Configuración inicial
setup() {
    echo -e "${YELLOW}📋 Configurando proyecto...${NC}"
    
    # Copiar archivos .env
    if [ ! -f protasker-api/.env ]; then
        cp protasker-api/.env.docker protasker-api/.env
        echo -e "${GREEN}✅ Archivo .env del backend creado${NC}"
    fi
    
    if [ ! -f protasker-web/.env ]; then
        cp protasker-web/.env.docker protasker-web/.env
        echo -e "${GREEN}✅ Archivo .env del frontend creado${NC}"
    fi
    
    # Construir imágenes
    echo -e "${YELLOW}🔨 Construyendo imágenes Docker...${NC}"
    docker-compose build
    
    # Iniciar servicios
    echo -e "${YELLOW}🚀 Iniciando servicios...${NC}"
    docker-compose up -d mysql
    
    # Esperar a que MySQL esté listo
    echo -e "${YELLOW}⏳ Esperando que MySQL esté listo...${NC}"
    sleep 30
    
    # Iniciar backend y frontend
    docker-compose up -d backend frontend
    
    echo -e "${GREEN}✅ Configuración completada!${NC}"
    echo -e "${BLUE}🌐 Frontend: http://localhost:5173${NC}"
    echo -e "${BLUE}🔗 Backend API: http://localhost:8000${NC}"
    echo -e "${BLUE}🗄️ MySQL: localhost:3306${NC}"
}

# Iniciar servicios
start() {
    echo -e "${GREEN}🚀 Iniciando servicios...${NC}"
    docker-compose up -d
}

# Detener servicios
stop() {
    echo -e "${RED}⏹️ Deteniendo servicios...${NC}"
    docker-compose down
}

# Reiniciar servicios
restart() {
    echo -e "${YELLOW}🔄 Reiniciando servicios...${NC}"
    docker-compose restart
}

# Ver logs
logs() {
    docker-compose logs -f
}

# Entrar al backend
backend() {
    echo -e "${BLUE}🔗 Entrando al contenedor del backend...${NC}"
    docker-compose exec backend bash
}

# Entrar al frontend
frontend() {
    echo -e "${BLUE}🎨 Entrando al contenedor del frontend...${NC}"
    docker-compose exec frontend sh
}

# Entrar a MySQL
mysql() {
    echo -e "${BLUE}🗄️ Entrando a MySQL...${NC}"
    docker-compose exec mysql mysql -u protasker_user -pprotasker_password protasker
}

# Reset base de datos
fresh() {
    echo -e "${YELLOW}🗃️ Reseteando base de datos...${NC}"
    docker-compose exec backend php artisan migrate:fresh --seed
}

# Reconstruir imágenes
build() {
    echo -e "${YELLOW}🔨 Reconstruyendo imágenes...${NC}"
    docker-compose build --no-cache
}

# Limpiar sistema
clean() {
    echo -e "${RED}🧹 Limpiando sistema Docker...${NC}"
    docker-compose down -v
    docker system prune -f
    docker volume prune -f
}

# Ver estado
status() {
    echo -e "${BLUE}📊 Estado de los servicios:${NC}"
    docker-compose ps
}

# Procesar comando
case $1 in
    setup)
        setup
        ;;
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        restart
        ;;
    logs)
        logs
        ;;
    backend)
        backend
        ;;
    frontend)
        frontend
        ;;
    mysql)
        mysql
        ;;
    fresh)
        fresh
        ;;
    build)
        build
        ;;
    clean)
        clean
        ;;
    status)
        status
        ;;
    help|*)
        show_help
        ;;
esac