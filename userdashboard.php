file name :- m_receipt
for maintenance receipt

code :-

<html>
 <head>
  <title>
   Maintenance Receipt
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
  <style>
   .stamp {
     position: relative;
     display: inline-block;
     padding: 20px;
     border: 4px solid black;
     border-radius: 50%;
     text-align: center;
     font-weight: bold;
     color: black;
     font-size: 1.25rem;
     width: 150px;
     height: 150px;
     line-height: 1.5;
   }
   .stamp::before, .stamp::after {
     content: '';
     position: absolute;
     top: 50%;
     left: 50%;
     width: 100%;
     height: 100%;
     border: 2px solid black;
     border-radius: 50%;
     transform: translate(-50%, -50%);
   }
   .stamp::after {
     width: 80%;
     height: 80%;
   }
  </style>
 </head>
 <body class="bg-gray-100 font-roboto">
  <div class="max-w-2xl mx-auto bg-white p-10 mt-10 shadow-2xl rounded-lg">
   <div class="text-center mb-8">
    <h1 class="text-4xl font-bold text-blue-600">
     Resinova Building
    </h1>
    <h2 class="text-2xl font-semibold text-gray-700 mt-2">
     Maintenance Receipt
    </h2>
   </div>
   <table class="w-full text-left table-auto">
    <tbody>
     <tr class="border-b">
      <th class="px-4 py-2 text-gray-700 font-bold">
       Flat Holder Name:
      </th>
      <td class="px-4 py-2 text-lg">
       John Doe
      </td>
     </tr>
     <tr class="border-b">
      <th class="px-4 py-2 text-gray-700 font-bold">
       Flat No:
      </th>
      <td class="px-4 py-2 text-lg">
       A-101
      </td>
     </tr>
     <tr class="border-b">
      <th class="px-4 py-2 text-gray-700 font-bold">
       Month:
      </th>
      <td class="px-4 py-2 text-lg">
       September 2023
      </td>
     </tr>
     <tr class="border-b">
      <th class="px-4 py-2 text-gray-700 font-bold">
       Amount (in words):
      </th>
      <td class="px-4 py-2 text-lg">
       Five Thousand Rupees
      </td>
     </tr>
     <tr class="border-b">
      <th class="px-4 py-2 text-gray-700 font-bold">
       Amount (in numbers):
      </th>
      <td class="px-4 py-2 text-lg">
       ₹5000
      </td>
     </tr>
     <tr class="border-b">
      <th class="px-4 py-2 text-gray-700 font-bold">
       Receipt No:
      </th>
      <td class="px-4 py-2 text-lg">
       123456
      </td>
     </tr>
    </tbody>
   </table>
   <div class="flex justify-end mt-8">
    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow-lg hover:bg-blue-700">
     Print Receipt
    </button>
   </div>
   <div class="flex justify-center mt-4">
    <div class="relative w-32 h-32">
     <div class="absolute inset-0 border-4 border-black rounded-full"></div>
     <div class="absolute inset-4 border-2 border-black rounded-full flex items-center justify-center">
      <div class="text-center">
       <p class="text-black font-bold text-lg">
        Resinova
       </p>
       <p class="text-black font-bold text-lg">
        Building
       </p>
       <p class="text-black font-bold text-lg">
        PAID
       </p>
      </div>
     </div>
    </div>
   </div>
  </div>
 </body>
</html>