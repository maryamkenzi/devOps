#!/bin/bash

# سكربت إعداد DevOps للتشغيل المحلي

echo "🚀 إعداد بيئة Akaunting DevOps..."

# التحقق من المتطلبات
echo "🔍 التحقق من المتطلبات..."
docker --version || { echo "❌ Docker غير مثبت"; exit 1; }
docker-compose --version || { echo "❌ Docker Compose غير مثبت"; exit 1; }
git --version || { echo "❌ Git غير مثبت"; exit 1; }

# بناء الصورة
echo "🐳 بناء صورة Docker..."
docker build -t akaunting-local .

# تشغيل الحاويات
echo "🚀 تشغيل الحاويات..."
docker-compose -f docker-compose.local.yml up -d

# الانتظار للتأكد من التشغيل
echo "⏳ انتظار بدء الخدمات..."
sleep 15

# عرض الحالة
echo "📊 حالة الخدمات:"
docker-compose -f docker-compose.local.yml ps

# عرض الروابط
echo ""
echo "🌐 روابط التطبيق:"
echo "   التطبيق: http://localhost:8000"
echo "   phpMyAdmin: http://localhost:8080"
echo "   قاعدة البيانات: localhost:3306"
echo ""
echo "🔧 معلومات الاتصال بقاعدة البيانات:"
echo "   Host: db"
echo "   Database: akaunting"
echo "   Username: akaunting_user"
echo "   Password: secret"
echo ""
echo "✅ الإعداد مكتمل!"
echo "💡 ملاحظة: AWS و Terraform موجودان للاستخدام المستقبلي"