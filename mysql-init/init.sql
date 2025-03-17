-- Create the database schema
CREATE TABLE IF NOT EXISTS developer_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    developer_name VARCHAR(100) NOT NULL,
    primary_language VARCHAR(50) NOT NULL,
    years_experience INT NOT NULL,
    project_type VARCHAR(50) NOT NULL,
    hours_per_week INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample data
INSERT INTO developer_data (developer_name, primary_language, years_experience, project_type, hours_per_week)
VALUES 
    ('Sameh ALi', 'PHP', 5, 'Web Application', 40),
    ('Andrew Smith', 'JavaScript', 3, 'Mobile App', 35),
    ('Jameel Johnson', 'Python', 7, 'Data Analysis', 45);