#!/bin/bash
echo "==================================================="
echo "  DANG CHAY CHE DO TU DONG PULL CODE (LINUX/MAC)"
echo "==================================================="
echo "Bấm Ctrl+C để dừng."
echo ""

while true; do
    git fetch origin >/dev/null 2>&1
    LOCAL=$(git rev-parse HEAD)
    REMOTE=$(git rev-parse @{u})

    if [ "$LOCAL" != "$REMOTE" ]; then
        echo "[$(date +'%H:%M:%S')] Phát hiện code mới! Đang tự động pull..."
        git pull
        echo "[$(date +'%H:%M:%S')] Đã cập nhật thành công!"
        echo "---------------------------------------------------"
    fi
    sleep 10
done
