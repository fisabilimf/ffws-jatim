from app import create_app
app = create_app()

# Jalankan:
# gunicorn -w 2 -b 0.0.0.0:8000 wsgi:app
