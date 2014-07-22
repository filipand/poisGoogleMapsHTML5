
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DBConnection {
	
	private static DBConnection instance;
	private Connection c;
	public static final String DB_PWD = "";
	public static final String DB_USER = "root";
	public static final String DB_URL = "jdbc:mysql://localhost:3306/phpmyadmin";
	
	private DBConnection(String url, String userName, String password) throws SQLException{
		try {
			Class.forName("com.mysql.jdbc.Driver");
		} catch (Exception e) {
			System.err.println("ERROR: failed to load MySQL JDBC driver.");
			e.printStackTrace();
		}

		c = DriverManager.getConnection(url, userName, password);
		
	}
	
	public synchronized static Connection getConnection() throws SQLException {
		if(instance == null) {
			
			instance = new DBConnection(DBConnection.DB_URL, DBConnection.DB_USER, DBConnection.DB_PWD);
		}
		return instance.c;
	}
	
	public void closeConnection(){
		try {
			if(c!=null){
				c.close();
			}
		} catch (SQLException e) {
		}
	}
	
	
	
	
}
