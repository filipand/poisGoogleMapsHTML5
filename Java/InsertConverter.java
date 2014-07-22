import java.beans.Statement;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.OutputStreamWriter;


import java.sql.Connection;
import java.sql.SQLException;
import java.util.Random;


public class InsertConverter {
	
	public static void main (String [] args){
		
		
		BufferedReader br = null;
		Connection connection = null;
		java.sql.Statement stmt = null;
		Random rand = new Random();
		
		
		try {
			connection = DBConnection.getConnection();
		} catch (SQLException e1) {
			e1.printStackTrace();
		}
		
		try {
			br = new BufferedReader(new FileReader("worldcitiespop.txt"));
			
			String line = "";
			
			int lineCount = 0;
			
			br.readLine();
			
			while((line = br.readLine()) != null) {
				
				int r = Math.abs(rand.nextInt());
				
				if(r % 15 != 0){
					continue;
				}
				
				lineCount ++;
				
				if(lineCount == 5000) {
					break;
				}
				
				int dotCount = 0;
				int lastDotIndex = 0;
				
				String cityName = "", latitude = "", longtitude = "";
				
				for(int i = 0; i < line.length(); i++){
					
					if(line.charAt(i) == ','){
						
						dotCount ++;
						if(dotCount == 3){
							cityName = line.substring(lastDotIndex + 1, i);
						}
						if(dotCount == 6){
							latitude = line.substring(lastDotIndex + 1, i);
						}
						
						lastDotIndex = i;
					}
					
					if( i == line.length() - 1){
						longtitude = line.substring(lastDotIndex + 1);
					}
					
					
					
				}
				
				r = rand.nextInt();
				
				String imagePath = "";
				String type = "";
				
				if(r % 3 == 0){
					type = "plane";
					imagePath = "http://www.airlines-inform.ru/images/aircraft/747-300-f.jpg";
					
				}else if(r % 3 == 1){
					type = "restaurant";
					imagePath = "http://www.russianriver.com/site/cms/images/me_applewood-restaurant.jpg";
				}else{
					type = "bar";
					imagePath = "http://www.romanaresort.com.vn/content/content_1958_2_thumb.jpg";
				}
				
			
				
				String insertStatement = "INSERT INTO `markers` (`name`, `address`, `lat`, `lng`, `type`, `imageLink`) VALUES ('"+cityName+"', '"+cityName+"', '"+latitude+"', '"+longtitude+"', '"+type+"', '"+imagePath+"');";
				
				
				stmt = connection.createStatement();
				stmt.executeUpdate(insertStatement);
				
				
			}
			
			stmt.close();
			connection.close();
			
			System.out.println(lineCount);
			
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		}
		catch (SQLException e ){
			e.printStackTrace();
		}
		catch (IOException e) {
			e.printStackTrace();
		}finally {
			try {
				br.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
		
	}
	
}
