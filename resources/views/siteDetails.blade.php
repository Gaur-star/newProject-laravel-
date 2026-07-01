@extends('layout')

@section('content')


    <div class="form-container">
        <h2>Site Details</h2>
        <form action="#" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="fullname">Site Name</label>
                <input type="text" id="sitename" name="sitename" placeholder="facebook" required>
            </div>

            <div class="form-group">
                <label for="fullname">Site Url</label>
                <input type="text" id="siteurl" name="siteurl" placeholder="facebook.com" required>
            </div>

            <div class="form-group">
                <label for="email">User Name</label>
                <input type="email" id="user" name="user" placeholder="john smith" required>
            </div>

            <div class="form-group">
                <label for="password">Site Application Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            {{-- <div class="form-group">
                <label for="profile-pic">Profile Picture</label>
                <input type="file" id="profile-pic" name="profile-pic" accept="image/*">
            </div> --}}

            <button type="submit" class="submit-btn">Submit</button>
            
        </form>
    </div>




    <style>
        
        * {
            /* margin: 0; */
            /* padding: 0; */
            /* box-sizing: border-box; */
            /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
        }

        body {
            /* background-color: #f3f4f6; */
            /* display: flex; */
            /* justify-content: center; */
            /* align-items: center; */
            /* min-height: 100vh; */
            /* padding: 20px; */
        }
      
        .form-container {
            /* background-color: #ffffff; */
            /* padding: 30px; */
            /* border-radius: 12px; */
            /* box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); */
            width: 100%;
            max-width: 450px;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #1f2937;
            font-size: 24px;
            text-align: center;
        }
       
        .form-group {
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 600;
            color: #4b5563;
        }
    
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
            color: #1f2937;
            outline: none;
            transition: all 0.2s ease-in-out;
        }
   
        .form-group input[type="file"] {
            padding: 5px 0;
            font-size: 14px;
            color: #4b5563;
        }
  
        .form-group input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
  
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4f46e5;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: #4338ca;
        }
    
    </style>

@endsection    