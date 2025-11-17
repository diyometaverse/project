import sys

def dummy_face_matcher(stored_path, live_path):
    # Simulate face matching logic
    # This is just placeholder logic
    return "MATCH" if stored_path and live_path else "NO_MATCH"

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: face_matcher.py <stored_face_path> <live_face_path>")
        sys.exit(1)
    
    stored_face_path = sys.argv[1]
    live_face_path = sys.argv[2]
    
    result = dummy_face_matcher(stored_face_path, live_face_path)
    print(result)