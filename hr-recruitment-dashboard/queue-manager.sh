#!/bin/bash

# Simple Queue Worker Manager for Shared Hosting
# No sudo/supervisord required

PROJECT_DIR="/path/to/your/laravel/project"
PIDFILE="$PROJECT_DIR/queue-worker.pid"
LOGFILE="$PROJECT_DIR/storage/logs/queue-worker.log"

start_queue() {
    if [ -f "$PIDFILE" ] && kill -0 $(cat "$PIDFILE") 2>/dev/null; then
        echo "Queue worker is already running (PID: $(cat $PIDFILE))"
        return 1
    fi
    
    echo "Starting Laravel queue worker..."
    cd "$PROJECT_DIR"
    nohup php artisan queue:work --sleep=3 --tries=3 --timeout=90 --daemon > "$LOGFILE" 2>&1 &
    echo $! > "$PIDFILE"
    echo "Queue worker started (PID: $!)"
}

stop_queue() {
    if [ -f "$PIDFILE" ] && kill -0 $(cat "$PIDFILE") 2>/dev/null; then
        echo "Stopping queue worker (PID: $(cat $PIDFILE))..."
        kill $(cat "$PIDFILE")
        rm -f "$PIDFILE"
        echo "Queue worker stopped"
    else
        echo "Queue worker is not running"
        rm -f "$PIDFILE"
    fi
}

status_queue() {
    if [ -f "$PIDFILE" ] && kill -0 $(cat "$PIDFILE") 2>/dev/null; then
        echo "Queue worker is running (PID: $(cat $PIDFILE))"
        echo "Log tail:"
        tail -5 "$LOGFILE"
    else
        echo "Queue worker is not running"
        rm -f "$PIDFILE"
    fi
}

restart_queue() {
    stop_queue
    sleep 2
    start_queue
}

case "$1" in
    start)
        start_queue
        ;;
    stop)
        stop_queue
        ;;
    restart)
        restart_queue
        ;;
    status)
        status_queue
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status}"
        echo "  start   - Start the queue worker"
        echo "  stop    - Stop the queue worker"
        echo "  restart - Restart the queue worker"
        echo "  status  - Check queue worker status"
        exit 1
        ;;
esac
