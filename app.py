from flask import Flask, render_template, request, redirect, url_for, session, flash
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import text
import sqlite3
# ! Penambahan library
from functools import wraps
import html

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///students.db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.secret_key = 'admin123'  # Tambahkan secret key untuk session
db = SQLAlchemy(app)


class Student(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    age = db.Column(db.Integer, nullable=False)
    grade = db.Column(db.String(10), nullable=False)

    def __repr__(self):
        return f'<Student {self.name}>'


# ! Penambahan def login_required(f)
# Digunakan untuk melindungi route CRUD yang perlu login
def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not session.get('logged_in'):
            flash('Please login first.', 'error')
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated_function
# ! Penambahan def login_required(f)


# ! Penambahan def login()
# Halaman Login atau route login 
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        if username == 'admin' and password == 'admin123': 
            session['logged_in'] = True
            session.modified = True  # Mark session as modified
            return redirect(url_for('index'))
        else:
            flash('Invalid username or password', 'error')
            return redirect(url_for('login'))
    return render_template('login.html')
# ! Penambahan def login()

# Halaman Utama
@app.route('/')
@login_required # !Penambahan @login_required 
def index():
    # RAW Query
    students = db.session.execute(text('SELECT * FROM student')).fetchall()
    return render_template('index.html', students=students)


@app.route('/add', methods=['POST'])
@login_required # !Penambahan @login_required
def add_student():
    name = html.escape(request.form['name']) # ! Penambahan html.escape(request.form['name'])
    age = request.form['age']
    grade = request.form['grade']
    
    connection = sqlite3.connect('instance/students.db')
    cursor = connection.cursor()

    # RAW Query
    query = f"INSERT INTO student (name, age, grade) VALUES ('{name}', {age}, '{grade}')"
    cursor.execute(query)
    connection.commit()
    connection.close()
    return redirect(url_for('index'))


@app.route('/delete/<int:id>') 
@login_required # !Penambahan @login_required
def delete_student(id):
    # Gunakan SQLAlchemy ORM untuk mencegah SQL Injection
    # ! Mengganti RAW Query menjadi SQLAlchemy ORM
    student = Student.query.get(id)  # Mendapatkan data student berdasarkan ID
    if student:
        db.session.delete(student)  # Hapus student
        db.session.commit()  # Commit perubahan
    # ! Mengganti RAW Query menjadi SQLAlchemy ORM
    
    return redirect(url_for('index'))

@app.route('/edit/<int:id>', methods=['GET', 'POST'])
@login_required # !Penambahan @login_required
def edit_student(id):
    if request.method == 'POST':
        name = html.escape(request.form['name']) # ! Penambahan html.escape(request.form['name'])
        age = request.form['age']
        grade = request.form['grade']
        
        # RAW Query
        db.session.execute(text(f"UPDATE student SET name='{name}', age={age}, grade='{grade}' WHERE id={id}"))
        db.session.commit()
        return redirect(url_for('index'))
    else:
        # RAW Query
        student = db.session.execute(text(f"SELECT * FROM student WHERE id={id}")).fetchone()
        return render_template('edit.html', student=student)

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
    app.run(host='0.0.0.0', port=5000, debug=True)
